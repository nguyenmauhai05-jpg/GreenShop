<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    /**
     * Build all data required by the Admin dashboard without coupling the
     * controller to reporting/query details.
     */
    public function build(?string $fromDate = null, ?string $toDate = null): array
    {
        [$tuNgay, $denNgay] = $this->resolveDateRange($fromDate, $toDate);

        $tongDoanhThu = DB::table('don_hang')
            ->whereIn('trang_thai', ['delivered', 'completed'])
            ->whereBetween('ngay_dat', [$tuNgay, $denNgay])
            ->sum('tong_tien');

        $tongDonHang = DB::table('don_hang')->count();
        $tongSanPham = DB::table('cay_canh')->count();
        $tongKhachHang = DB::table('nguoi_dung')->count();

        $donChoXuLy = DB::table('don_hang')
            ->where('trang_thai', 'pending_confirmation')
            ->count();

        $caySapHetHang = DB::table('cay_canh')
            ->where('so_luong', '>', 0)
            ->where('so_luong', '<=', 15)
            ->orderBy('so_luong')
            ->limit(5)
            ->get();

        $topCayBanChay = DB::table('chi_tiet_don_hang as ct')
            ->join('cay_canh as cc', 'cc.plant_id', '=', 'ct.plant_id')
            ->join('don_hang as dh', 'dh.order_id', '=', 'ct.order_id')
            ->whereIn('dh.trang_thai', ['delivered', 'completed'])
            ->select(
                'cc.plant_id',
                'cc.ten_cay',
                'cc.anh_dai_dien',
                DB::raw('SUM(ct.so_luong) as da_ban'),
                DB::raw('SUM(ct.so_luong * ct.don_gia) as doanh_thu')
            )
            ->groupBy('cc.plant_id', 'cc.ten_cay', 'cc.anh_dai_dien')
            ->orderByDesc('da_ban')
            ->limit(5)
            ->get();

        [$doanhThu7Ngay, $doanhThuMax] = $this->revenueSeries($tuNgay, $denNgay);

        return [
            'tongDoanhThu' => $tongDoanhThu,
            'tongDonHang' => $tongDonHang,
            'tongSanPham' => $tongSanPham,
            'tongKhachHang' => $tongKhachHang,
            'donChoXuLy' => $donChoXuLy,
            'caySapHetHang' => $caySapHetHang,
            'topCayBanChay' => $topCayBanChay,
            'doanhThu7Ngay' => $doanhThu7Ngay,
            'doanhThuMax' => $doanhThuMax,
            'hoatDongGanDay' => $this->recentLowReviews(),
            'tuNgay' => $tuNgay,
            'denNgay' => $denNgay,
        ];
    }

    public function emptyState(): array
    {
        return [
            'tongDoanhThu' => 0,
            'tongDonHang' => 0,
            'tongSanPham' => 0,
            'tongKhachHang' => 0,
            'donChoXuLy' => 0,
            'caySapHetHang' => collect(),
            'topCayBanChay' => collect(),
            'doanhThu7Ngay' => collect(),
            'doanhThuMax' => 1,
            'hoatDongGanDay' => collect(),
            'tuNgay' => now()->subDays(6)->startOfDay(),
            'denNgay' => now()->endOfDay(),
        ];
    }

    private function resolveDateRange(?string $fromDate, ?string $toDate): array
    {
        $denNgay = $toDate
            ? Carbon::parse($toDate)->endOfDay()
            : now()->endOfDay();

        $tuNgay = $fromDate
            ? Carbon::parse($fromDate)->startOfDay()
            : $denNgay->copy()->subDays(6)->startOfDay();

        if ($tuNgay->gt($denNgay)) {
            [$tuNgay, $denNgay] = [
                $denNgay->copy()->startOfDay(),
                $tuNgay->copy()->endOfDay(),
            ];
        }

        if ($tuNgay->diffInDays($denNgay) > 30) {
            $tuNgay = $denNgay->copy()->subDays(29)->startOfDay();
        }

        return [$tuNgay, $denNgay];
    }

    private function revenueSeries(Carbon $tuNgay, Carbon $denNgay): array
    {
        $doanhThuTheoNgay = DB::table('don_hang')
            ->whereIn('trang_thai', ['delivered', 'completed'])
            ->whereBetween('ngay_dat', [$tuNgay, $denNgay])
            ->selectRaw('DATE(ngay_dat) as ngay_db, SUM(tong_tien) as doanh_thu')
            ->groupByRaw('DATE(ngay_dat)')
            ->pluck('doanh_thu', 'ngay_db');

        $series = collect();
        $cursor = $tuNgay->copy()->startOfDay();

        while ($cursor->lte($denNgay)) {
            $key = $cursor->format('Y-m-d');
            $series->push((object) [
                'ngay' => $cursor->format('d/m'),
                'ngay_day_du' => $cursor->format('d/m/Y'),
                'doanh_thu' => (float) ($doanhThuTheoNgay[$key] ?? 0),
            ]);
            $cursor->addDay();
        }

        return [
            $series,
            max(1, (float) ($series->max('doanh_thu') ?? 0)),
        ];
    }

    private function recentLowReviews(): Collection
    {
        return DB::table('danh_gia as dg')
            ->leftJoin('nguoi_dung as nd', 'nd.user_id', '=', 'dg.user_id')
            ->leftJoin('cay_canh as cc', 'cc.plant_id', '=', 'dg.plant_id')
            ->whereNotNull('dg.so_sao')
            ->where('dg.so_sao', '<=', 2)
            ->select(
                'dg.review_id',
                'dg.so_sao',
                'dg.noi_dung',
                'dg.ngay_danh_gia',
                'nd.ho_ten',
                'cc.ten_cay'
            )
            ->orderByDesc('dg.ngay_danh_gia')
            ->orderByDesc('dg.review_id')
            ->limit(5)
            ->get()
            ->map(function ($review) {
                $tenKhach = trim((string) ($review->ho_ten ?? 'Khách hàng'));
                $tenCay = trim((string) ($review->ten_cay ?? 'sản phẩm'));
                $noiDung = trim((string) ($review->noi_dung ?? ''));

                if (mb_strlen($noiDung) > 70) {
                    $noiDung = mb_substr($noiDung, 0, 67) . '...';
                }

                $review->icon = '★';
                $review->noi_dung_hien_thi = $tenKhach
                    . ' đánh giá '
                    . (int) $review->so_sao
                    . ' sao cho '
                    . $tenCay
                    . ($noiDung !== '' ? ': “' . $noiDung . '”' : '');

                $review->thoi_gian_hien_thi = $review->ngay_danh_gia
                    ? Carbon::parse($review->ngay_danh_gia)->format('d/m/Y H:i')
                    : '';

                return $review;
            });
    }
}
