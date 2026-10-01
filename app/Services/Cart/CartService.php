<?php

namespace App\Services\Cart;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Dữ liệu hiển thị trang giỏ hàng.
     *
     * @return array{items: Collection, tongLoaiSanPham:int, tongSoLuong:int, tamTinh:float|int, gioHopLe:bool}
     */
    public function viewData(int $userId): array
    {
        $cart = DB::table('gio_hang')
            ->where('user_id', $userId)
            ->first();

        if (!$cart) {
            return [
                'items' => collect(),
                'tongLoaiSanPham' => 0,
                'tongSoLuong' => 0,
                'tamTinh' => 0,
                'gioHopLe' => true,
            ];
        }

        $items = DB::table('chi_tiet_gio_hang as ct')
            ->join('cay_canh as cc', 'cc.plant_id', '=', 'ct.plant_id')
            ->leftJoin('danh_muc as dm', 'dm.category_id', '=', 'cc.category_id')
            ->where('ct.cart_id', $cart->cart_id)
            ->select(
                'ct.cart_detail_id',
                'ct.cart_id',
                'ct.plant_id',
                'ct.so_luong',
                'cc.ten_cay',
                'cc.gia',
                'cc.so_luong as so_luong_ton',
                'cc.chieu_cao',
                'cc.anh_dai_dien',
                'cc.trang_thai',
                'dm.ten_danh_muc'
            )
            ->get()
            ->map(function ($item) {
                $item->san_pham_hop_le = $item->trang_thai === 'Đang bán';
                $item->gia_hien_tai = (float) $item->gia;
                $item->thanh_tien = $item->gia_hien_tai * (int) $item->so_luong;
                $item->update_url = route('gio-hang.cap-nhat', $item->cart_detail_id);
                $item->delete_url = route('gio-hang.xoa', $item->cart_detail_id);

                return $item;
            });

        $gioHopLe = !$items->contains(function ($item) {
            return !$item->san_pham_hop_le
                || $item->so_luong_ton <= 0
                || $item->so_luong <= 0
                || $item->so_luong > $item->so_luong_ton;
        });

        return [
            'items' => $items,
            'tongLoaiSanPham' => $items->count(),
            'tongSoLuong' => (int) $items->sum(fn ($item) => (int) $item->so_luong),
            'tamTinh' => $items->sum(fn ($item) => $item->thanh_tien),
            'gioHopLe' => $gioHopLe,
        ];
    }

    public function ownedLineForUpdate(int $userId, int|string $cartDetailId): ?object
    {
        return DB::table('chi_tiet_gio_hang as ct')
            ->join('gio_hang as gh', 'gh.cart_id', '=', 'ct.cart_id')
            ->leftJoin('cay_canh as cc', 'cc.plant_id', '=', 'ct.plant_id')
            ->where('ct.cart_detail_id', $cartDetailId)
            ->where('gh.user_id', $userId)
            ->select(
                'ct.cart_detail_id',
                'ct.so_luong',
                'ct.plant_id',
                'cc.so_luong as so_luong_ton',
                'cc.trang_thai'
            )
            ->first();
    }

    public function updateQuantity(int|string $cartDetailId, int $quantity): void
    {
        DB::table('chi_tiet_gio_hang')
            ->where('cart_detail_id', $cartDetailId)
            ->update(['so_luong' => $quantity]);
    }

    public function ownedLineForDelete(int $userId, int|string $cartDetailId): ?object
    {
        return DB::table('chi_tiet_gio_hang as ct')
            ->join('gio_hang as gh', 'gh.cart_id', '=', 'ct.cart_id')
            ->where('ct.cart_detail_id', $cartDetailId)
            ->where('gh.user_id', $userId)
            ->select('ct.cart_detail_id', 'ct.cart_id')
            ->first();
    }

    public function deleteLine(int|string $cartDetailId): void
    {
        DB::table('chi_tiet_gio_hang')
            ->where('cart_detail_id', $cartDetailId)
            ->delete();
    }

    public function plant(int $plantId): ?object
    {
        return DB::table('cay_canh')
            ->where('plant_id', $plantId)
            ->first();
    }

    /** Tổng số lượng sản phẩm trong giỏ để cập nhật badge sau AJAX. */
    public function countForUser(int $userId): int
    {
        return (int) DB::table('gio_hang as gh')
            ->join('chi_tiet_gio_hang as ct', 'ct.cart_id', '=', 'gh.cart_id')
            ->where('gh.user_id', $userId)
            ->sum('ct.so_luong');
    }

    /**
     * Thêm sản phẩm vào giỏ. Có lock để tránh cộng số lượng bị race-condition.
     */
    public function add(int $userId, object $plant, int $quantity): void
    {
        DB::transaction(function () use ($userId, $plant, $quantity) {
            $cart = DB::table('gio_hang')
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if (!$cart) {
                $cartId = DB::table('gio_hang')->insertGetId([
                    'user_id' => $userId,
                ], 'cart_id');
            } else {
                $cartId = $cart->cart_id;
            }

            $line = DB::table('chi_tiet_gio_hang')
                ->where('cart_id', $cartId)
                ->where('plant_id', $plant->plant_id)
                ->lockForUpdate()
                ->first();

            if (!$line) {
                DB::table('chi_tiet_gio_hang')->insert([
                    'cart_id' => $cartId,
                    'plant_id' => $plant->plant_id,
                    'so_luong' => $quantity,
                ]);

                return;
            }

            $newQuantity = (int) $line->so_luong + $quantity;
            if ($newQuantity > (int) $plant->so_luong) {
                throw new \RuntimeException('SO_LUONG_VUOT_TON');
            }

            DB::table('chi_tiet_gio_hang')
                ->where('cart_detail_id', $line->cart_detail_id)
                ->update(['so_luong' => $newQuantity]);
        });
    }
}
