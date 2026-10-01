<?php

namespace App\Services\Plants;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlantQueryService
{
    public function getIndexData(Request $request): array
    {
        $query = $this->baseQuery();
        $this->applyIndexFilters($query, $request);

        $cayCanhs = $query
            ->orderByDesc('cc.plant_id')
            ->paginate(10)
            ->withQueryString();

        return [
            'cayCanhs' => $cayCanhs,
            'danhMucs' => $this->getCategories(),
            ...$this->getStatistics(),
        ];
    }

    public function getCategories(): Collection
    {
        return DB::table('danh_muc')
            ->select('category_id', 'ten_danh_muc')
            ->orderBy('ten_danh_muc')
            ->get();
    }

    public function getExportPlants(Request $request): Collection
    {
        $query = DB::table('cay_canh as cc')
            ->leftJoin('danh_muc as dm', 'dm.category_id', '=', 'cc.category_id')
            ->select(
                'cc.plant_id',
                'cc.ten_cay',
                'dm.ten_danh_muc',
                'cc.gia',
                'cc.so_luong',
                'cc.chieu_cao',
                'cc.trang_thai'
            );

        $this->applyExportFilters($query, $request);

        return $query
            ->orderByDesc('cc.plant_id')
            ->get();
    }

    private function baseQuery(): Builder
    {
        return DB::table('cay_canh as cc')
            ->leftJoin('danh_muc as dm', 'dm.category_id', '=', 'cc.category_id')
            ->select(
                'cc.plant_id',
                'cc.category_id',
                'cc.ten_cay',
                'cc.gia',
                'cc.so_luong',
                'cc.mo_ta',
                'cc.cach_cham_soc',
                'cc.chieu_cao',
                'cc.anh_dai_dien',
                'cc.trang_thai',
                'dm.ten_danh_muc'
            );
    }

    private function applyIndexFilters(Builder $query, Request $request): void
    {
        $keyword = trim((string) $request->input('search', ''));

        if ($keyword !== '') {
            $query->where(function (Builder $q) use ($keyword): void {
                $q->where('cc.ten_cay', 'LIKE', '%' . $keyword . '%');

                // Giữ nguyên nghiệp vụ cũ: tìm được cả "12" lẫn "CC012".
                $maSo = preg_replace('/[^0-9]/', '', $keyword);

                if ($maSo !== '') {
                    $q->orWhere('cc.plant_id', (int) $maSo);
                }
            });
        }

        $this->applyCommonFilters($query, $request);
    }

    private function applyExportFilters(Builder $query, Request $request): void
    {
        $keyword = trim((string) $request->input('search', ''));

        if ($keyword !== '') {
            $query->where(function (Builder $q) use ($keyword): void {
                $q->where('cc.ten_cay', 'LIKE', '%' . $keyword . '%');

                // Giữ nguyên nghiệp vụ export cũ: mã cây phải dạng CC001, CC002...
                if (preg_match('/^CC0*(\d+)$/i', $keyword, $matches)) {
                    $q->orWhere('cc.plant_id', (int) $matches[1]);
                }
            });
        }

        $this->applyCommonFilters($query, $request);
    }

    private function applyCommonFilters(Builder $query, Request $request): void
    {
        if ($request->filled('category')) {
            $query->where('cc.category_id', $request->input('category'));
        }

        if ($request->filled('status')) {
            $query->where('cc.trang_thai', $request->input('status'));
        }

        $stock = $request->input('stock');

        if ($stock === 'con-hang') {
            $query->where('cc.so_luong', '>', 10);
        } elseif ($stock === 'sap-het') {
            $query
                ->where('cc.so_luong', '>', 0)
                ->where('cc.so_luong', '<=', 10);
        } elseif ($stock === 'het-hang') {
            $query->where('cc.so_luong', '<=', 0);
        }
    }

    private function getStatistics(): array
    {
        return [
            'tongCay' => DB::table('cay_canh')->count(),
            'tongDangBan' => DB::table('cay_canh')
                ->where('trang_thai', 'Đang bán')
                ->count(),
            'tongSapHet' => DB::table('cay_canh')
                ->where('so_luong', '>', 0)
                ->where('so_luong', '<=', 10)
                ->count(),
            'tongHetHang' => DB::table('cay_canh')
                ->where('so_luong', '<=', 0)
                ->count(),
        ];
    }
}
