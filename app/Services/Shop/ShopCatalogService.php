<?php

namespace App\Services\Shop;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShopCatalogService
{
    /**
     * Lấy danh sách cây theo search/filter/sort hiện tại.
     *
     * Lưu ý về chiều cao:
     * DB đang lưu `cay_canh.chieu_cao` dạng VARCHAR như "30-50 cm".
     * Code cũ để MySQL tự ép chuỗi sang số khi so sánh. Để không đổi nghiệp
     * vụ đang chạy, service này làm phép ép kiểu đó một cách tường minh;
     * vì vậy "30-50 cm" vẫn được phân loại theo số đầu tiên là 30.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters, int $perPage = 8): LengthAwarePaginator
    {
        $query = DB::table('cay_canh as cc')
            ->leftJoin('danh_muc as dm', 'dm.category_id', '=', 'cc.category_id')
            ->select(
                'cc.plant_id',
                'cc.category_id',
                'cc.ten_cay',
                'cc.gia',
                'cc.so_luong',
                'cc.mo_ta',
                'cc.chieu_cao',
                'cc.anh_dai_dien',
                'cc.trang_thai',
                'dm.ten_danh_muc'
            )
            ->where('cc.trang_thai', 'Đang bán');

        if ($filters['keyword'] !== '') {
            $keyword = $filters['keyword'];

            $query->where(function ($q) use ($keyword): void {
                $q->where('cc.ten_cay', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('dm.ten_danh_muc', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('cc.mo_ta', 'LIKE', '%' . $keyword . '%');
            });
        }

        if ($filters['filtersValid']) {
            $this->applyCategory($query, $filters['category']);
            $this->applyPrice($query, $filters['minPrice'], $filters['maxPrice']);
            $this->applyStockStatus($query, $filters['statuses']);
            $this->applySize($query, $filters['sizes']);
        }

        $this->applySort($query, $filters['sort']);

        return $query->paginate($perPage)->withQueryString();
    }

    public function categories(): Collection
    {
        return DB::table('danh_muc as dm')
            ->leftJoin('cay_canh as cc', function ($join): void {
                $join->on('cc.category_id', '=', 'dm.category_id')
                    ->where('cc.trang_thai', '=', 'Đang bán');
            })
            ->select(
                'dm.category_id',
                'dm.ten_danh_muc',
                DB::raw('COUNT(cc.plant_id) as tong_so_cay')
            )
            ->groupBy('dm.category_id', 'dm.ten_danh_muc')
            ->orderBy('dm.ten_danh_muc')
            ->get();
    }

    public function maxPrice(): float
    {
        return (float) (DB::table('cay_canh')
            ->where('trang_thai', 'Đang bán')
            ->max('gia') ?? 0);
    }

    private function applyCategory($query, mixed $category): void
    {
        if ($category !== null && $category !== '') {
            $query->where('cc.category_id', '=', $category);
        }
    }

    private function applyPrice($query, ?float $minPrice, ?float $maxPrice): void
    {
        if ($minPrice !== null) {
            $query->where('cc.gia', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('cc.gia', '<=', $maxPrice);
        }
    }

    /**
     * @param array<int, string> $statuses
     */
    private function applyStockStatus($query, array $statuses): void
    {
        $hasInStock = in_array('con-hang', $statuses, true);
        $hasOutOfStock = in_array('het-hang', $statuses, true);

        if ($hasInStock && ! $hasOutOfStock) {
            $query->where('cc.so_luong', '>', 0);
        } elseif ($hasOutOfStock && ! $hasInStock) {
            $query->where('cc.so_luong', '<=', 0);
        }
    }

    /**
     * @param array<int, string> $sizes
     */
    private function applySize($query, array $sizes): void
    {
        if ($sizes === []) {
            return;
        }

        // Giữ đúng hành vi cũ của MySQL: chuỗi "30-50 cm" được hiểu là 30.
        $heightExpression = "CAST(NULLIF(TRIM(cc.chieu_cao), '') AS DECIMAL(10,2))";

        $query->where(function ($q) use ($sizes, $heightExpression): void {
            if (in_array('small', $sizes, true)) {
                $q->orWhereRaw($heightExpression . ' < ?', [30]);
            }

            if (in_array('medium', $sizes, true)) {
                $q->orWhereRaw($heightExpression . ' BETWEEN ? AND ?', [30, 80]);
            }

            if (in_array('large', $sizes, true)) {
                $q->orWhereRaw($heightExpression . ' > ?', [80]);
            }
        });
    }

    private function applySort($query, string $sort): void
    {
        switch ($sort) {
            case 'price-asc':
                $query->orderBy('cc.gia');
                break;

            case 'price-desc':
                $query->orderByDesc('cc.gia');
                break;

            case 'best-selling':
                // Giữ hành vi hiện tại: chưa thay đổi sang thống kê số lượng đã bán.
                $query->orderByDesc('cc.plant_id');
                break;

            case 'newest':
            default:
                $query->orderByDesc('cc.plant_id');
                break;
        }
    }
}
