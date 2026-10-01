<?php

namespace App\Services\Reports;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportDataService
{
    private const REVENUE_STATUSES = [
        'delivered',
        'completed',
    ];

    public function getIndexData(Request $request, array $filters): array
    {
        /** @var Carbon $tuNgay */
        $tuNgay = $filters['tuNgay'];
        /** @var Carbon $denNgay */
        $denNgay = $filters['denNgay'];
        $categoryId = $filters['categoryId'];
        $orderStatus = $filters['orderStatus'];

        $donHangQuery = $this->baseOrderQuery(
            $tuNgay,
            $denNgay,
            $categoryId,
            $orderStatus
        );

        $tongDonHang = (clone $donHangQuery)->count();

        $doanhThuQuery = clone $donHangQuery;
        if (!$orderStatus) {
            $doanhThuQuery->whereIn('dh.trang_thai', self::REVENUE_STATUSES);
        }

        $tongDoanhThu = (float) ($doanhThuQuery->sum('dh.tong_tien') ?? 0);

        $sanPhamDaBanQuery = DB::table('chi_tiet_don_hang as ct')
            ->join('don_hang as dh', 'dh.order_id', '=', 'ct.order_id')
            ->join('cay_canh as cc', 'cc.plant_id', '=', 'ct.plant_id')
            ->whereBetween('dh.ngay_dat', [$tuNgay, $denNgay]);

        $this->applyRevenueStatus($sanPhamDaBanQuery, $orderStatus);

        if ($categoryId) {
            $sanPhamDaBanQuery->where('cc.category_id', $categoryId);
        }

        $tongSanPhamDaBan = (int) ($sanPhamDaBanQuery->sum('ct.so_luong') ?? 0);

        $tongKhachHangMoi = DB::table('nguoi_dung as nd')
            ->join('vai_tro as vt', 'vt.role_id', '=', 'nd.role_id')
            ->whereRaw('LOWER(TRIM(vt.ten_vai_tro)) <> ?', ['admin'])
            ->whereBetween('nd.created_at', [$tuNgay, $denNgay])
            ->count();

        $duLieuBieuDo = $this->getChartData(
            $tuNgay,
            $denNgay,
            $filters['nhomTheo'],
            $categoryId,
            $orderStatus
        );

        $doanhThuDanhMuc = $this->getCategoryRevenue(
            $tuNgay,
            $denNgay,
            $categoryId,
            $orderStatus
        );

        $sanPhamBaoCao = $this->getProductReport(
            $request,
            $tuNgay,
            $denNgay,
            $categoryId,
            $orderStatus
        );

        $thongKeTrangThai = $this->getOrderStatusStatistics(
            $tuNgay,
            $denNgay,
            $categoryId
        );

        return [
            ...$filters,
            'danhMucs' => $this->getCategories(),
            'tongDoanhThu' => $tongDoanhThu,
            'tongDonHang' => $tongDonHang,
            'tongSanPhamDaBan' => $tongSanPhamDaBan,
            'tongKhachHangMoi' => $tongKhachHangMoi,
            'duLieuBieuDo' => $duLieuBieuDo,
            'doanhThuDanhMuc' => $doanhThuDanhMuc,
            'sanPhamBaoCao' => $sanPhamBaoCao,
            'thongKeTrangThai' => $thongKeTrangThai,
            'khongCoDuLieu' => $tongDonHang === 0
                && $tongDoanhThu == 0
                && $tongSanPhamDaBan === 0
                && $tongKhachHangMoi === 0,
        ];
    }

    public function getCategories(): Collection
    {
        return DB::table('danh_muc')
            ->select('category_id', 'ten_danh_muc')
            ->orderBy('ten_danh_muc')
            ->get();
    }

    public function getExportSummary(array $filters): array
    {
        $tuNgay = $filters['tuNgay'];
        $denNgay = $filters['denNgay'];
        $categoryId = $filters['categoryId'];
        $orderStatus = $filters['orderStatus'];

        $sanPhams = $this->getExportProducts(
            $tuNgay,
            $denNgay,
            $categoryId,
            $orderStatus
        );

        $donHangQuery = $this->baseOrderQuery(
            $tuNgay,
            $denNgay,
            $categoryId,
            $orderStatus
        );

        return [
            'tuNgay' => $tuNgay,
            'denNgay' => $denNgay,
            'sanPhams' => $sanPhams,
            'tongDoanhThu' => (float) $sanPhams->sum('doanh_thu'),
            'tongSanPham' => (int) $sanPhams->sum('so_luong_da_ban'),
            'tongDonHang' => $donHangQuery->count(),
        ];
    }

    private function baseOrderQuery(
        Carbon $tuNgay,
        Carbon $denNgay,
        ?int $categoryId,
        ?string $orderStatus
    ): Builder {
        $query = DB::table('don_hang as dh')
            ->whereBetween('dh.ngay_dat', [$tuNgay, $denNgay]);

        if ($orderStatus) {
            $query->where('dh.trang_thai', $orderStatus);
        }

        if ($categoryId) {
            $query->whereExists(function (Builder $subQuery) use ($categoryId): void {
                $subQuery
                    ->select(DB::raw(1))
                    ->from('chi_tiet_don_hang as ct_filter')
                    ->join(
                        'cay_canh as cc_filter',
                        'cc_filter.plant_id',
                        '=',
                        'ct_filter.plant_id'
                    )
                    ->whereColumn('ct_filter.order_id', 'dh.order_id')
                    ->where('cc_filter.category_id', $categoryId);
            });
        }

        return $query;
    }

    private function getChartData(
        Carbon $tuNgay,
        Carbon $denNgay,
        string $nhomTheo,
        ?int $categoryId,
        ?string $orderStatus
    ): Collection {
        $groupSql = match ($nhomTheo) {
            'thang' => "DATE_FORMAT(dh.ngay_dat, '%Y-%m')",
            'nam' => 'YEAR(dh.ngay_dat)',
            default => 'DATE(dh.ngay_dat)',
        };

        $query = DB::table('don_hang as dh')
            ->whereBetween('dh.ngay_dat', [$tuNgay, $denNgay]);

        $this->applyRevenueStatus($query, $orderStatus);

        if ($categoryId) {
            $query->whereExists(function (Builder $subQuery) use ($categoryId): void {
                $subQuery
                    ->select(DB::raw(1))
                    ->from('chi_tiet_don_hang as ct')
                    ->join('cay_canh as cc', 'cc.plant_id', '=', 'ct.plant_id')
                    ->whereColumn('ct.order_id', 'dh.order_id')
                    ->where('cc.category_id', $categoryId);
            });
        }

        return $query
            ->selectRaw($groupSql . ' AS nhom')
            ->selectRaw('SUM(dh.tong_tien) AS doanh_thu')
            ->groupByRaw($groupSql)
            ->orderByRaw($groupSql)
            ->get();
    }

    private function getCategoryRevenue(
        Carbon $tuNgay,
        Carbon $denNgay,
        ?int $categoryId,
        ?string $orderStatus
    ): Collection {
        $query = DB::table('chi_tiet_don_hang as ct')
            ->join('don_hang as dh', 'dh.order_id', '=', 'ct.order_id')
            ->join('cay_canh as cc', 'cc.plant_id', '=', 'ct.plant_id')
            ->leftJoin('danh_muc as dm', 'dm.category_id', '=', 'cc.category_id')
            ->whereBetween('dh.ngay_dat', [$tuNgay, $denNgay]);

        $this->applyRevenueStatus($query, $orderStatus);

        if ($categoryId) {
            $query->where('cc.category_id', $categoryId);
        }

        $data = $query
            ->select('dm.category_id', 'dm.ten_danh_muc')
            ->selectRaw('SUM(ct.don_gia * ct.so_luong) AS doanh_thu')
            ->groupBy('dm.category_id', 'dm.ten_danh_muc')
            ->orderByDesc('doanh_thu')
            ->get();

        $tong = (float) $data->sum('doanh_thu');

        return $data->map(function ($item) use ($tong) {
            $item->ty_le = $tong > 0
                ? round(((float) $item->doanh_thu / $tong) * 100, 1)
                : 0;

            return $item;
        });
    }

    private function getProductReport(
        Request $request,
        Carbon $tuNgay,
        Carbon $denNgay,
        ?int $categoryId,
        ?string $orderStatus
    ) {
        $query = $this->baseProductRevenueQuery(
            $tuNgay,
            $denNgay,
            $categoryId,
            $orderStatus
        );

        $tongDoanhThuSanPham = (float) (
            (clone $query)
                ->selectRaw('SUM(ct.don_gia * ct.so_luong) AS tong')
                ->value('tong')
            ?? 0
        );

        $result = $query
            ->select(
                'cc.plant_id',
                'cc.ten_cay',
                'cc.anh_dai_dien',
                'dm.ten_danh_muc'
            )
            ->selectRaw('SUM(ct.so_luong) AS so_luong_da_ban')
            ->selectRaw('SUM(ct.don_gia * ct.so_luong) AS doanh_thu')
            ->groupBy(
                'cc.plant_id',
                'cc.ten_cay',
                'cc.anh_dai_dien',
                'dm.ten_danh_muc'
            )
            ->orderByDesc('doanh_thu')
            ->paginate(10, ['*'], 'product_page')
            ->withQueryString();

        foreach ($result as $item) {
            $item->ty_le = $tongDoanhThuSanPham > 0
                ? round(((float) $item->doanh_thu / $tongDoanhThuSanPham) * 100, 1)
                : 0;
        }

        return $result;
    }

    private function getOrderStatusStatistics(
        Carbon $tuNgay,
        Carbon $denNgay,
        ?int $categoryId
    ): Collection {
        $query = DB::table('don_hang as dh')
            ->whereBetween('dh.ngay_dat', [$tuNgay, $denNgay]);

        if ($categoryId) {
            $query->whereExists(function (Builder $subQuery) use ($categoryId): void {
                $subQuery
                    ->select(DB::raw(1))
                    ->from('chi_tiet_don_hang as ct')
                    ->join('cay_canh as cc', 'cc.plant_id', '=', 'ct.plant_id')
                    ->whereColumn('ct.order_id', 'dh.order_id')
                    ->where('cc.category_id', $categoryId);
            });
        }

        $data = $query
            ->select('dh.trang_thai')
            ->selectRaw('COUNT(*) AS so_don')
            ->selectRaw('SUM(dh.tong_tien) AS doanh_thu')
            ->groupBy('dh.trang_thai')
            ->orderByDesc('so_don')
            ->get();

        $tongDon = (int) $data->sum('so_don');

        return $data->map(function ($item) use ($tongDon) {
            $item->ty_le = $tongDon > 0
                ? round(($item->so_don / $tongDon) * 100, 1)
                : 0;
            $item->ten_trang_thai = $this->getStatusName($item->trang_thai);

            return $item;
        });
    }

    private function getExportProducts(
        Carbon $tuNgay,
        Carbon $denNgay,
        ?int $categoryId,
        ?string $orderStatus
    ): Collection {
        return $this->baseProductRevenueQuery(
            $tuNgay,
            $denNgay,
            $categoryId,
            $orderStatus
        )
            ->select('cc.plant_id', 'cc.ten_cay', 'dm.ten_danh_muc')
            ->selectRaw('SUM(ct.so_luong) AS so_luong_da_ban')
            ->selectRaw('SUM(ct.don_gia * ct.so_luong) AS doanh_thu')
            ->groupBy('cc.plant_id', 'cc.ten_cay', 'dm.ten_danh_muc')
            ->orderByDesc('doanh_thu')
            ->get();
    }

    private function baseProductRevenueQuery(
        Carbon $tuNgay,
        Carbon $denNgay,
        ?int $categoryId,
        ?string $orderStatus
    ): Builder {
        $query = DB::table('chi_tiet_don_hang as ct')
            ->join('don_hang as dh', 'dh.order_id', '=', 'ct.order_id')
            ->join('cay_canh as cc', 'cc.plant_id', '=', 'ct.plant_id')
            ->leftJoin('danh_muc as dm', 'dm.category_id', '=', 'cc.category_id')
            ->whereBetween('dh.ngay_dat', [$tuNgay, $denNgay]);

        $this->applyRevenueStatus($query, $orderStatus);

        if ($categoryId) {
            $query->where('cc.category_id', $categoryId);
        }

        return $query;
    }

    private function applyRevenueStatus(Builder $query, ?string $orderStatus): void
    {
        if ($orderStatus) {
            $query->where('dh.trang_thai', $orderStatus);
        } else {
            $query->whereIn('dh.trang_thai', self::REVENUE_STATUSES);
        }
    }

    private function getStatusName(?string $status): string
    {
        return match ($status) {
            'pending_confirmation' => 'Chờ xác nhận',
            'preparing' => 'Đang chuẩn bị',
            'shipping' => 'Đang giao',
            'delivered' => 'Đã giao',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            default => $status ?: 'Không xác định',
        };
    }
}
