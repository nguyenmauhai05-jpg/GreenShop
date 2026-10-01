<?php

namespace App\Services\Checkout;

use App\Models\DonHang;

class CheckoutIntegrityService
{
    public function generateOrderCode(): string
    {
        $prefix = 'GS' . now()->format('ymd') . '-';
        $start = random_int(0, 9999);

        for ($attempt = 0; $attempt < 10000; $attempt++) {
            $suffix = ($start + $attempt) % 10000;
            $code = $prefix . str_pad((string) $suffix, 4, '0', STR_PAD_LEFT);

            if (!DonHang::where('order_code', $code)->exists()) {
                return $code;
            }
        }

        throw new \RuntimeException('Không còn mã đơn hàng khả dụng cho hôm nay.');
    }

    public function cartSignature(iterable $details): string
    {
        $items = collect($details)->map(function ($detail) {
            $plant = $detail->cayCanh ?? null;

            return [
                'plant_id' => (int) $detail->plant_id,
                'quantity' => (int) $detail->so_luong,
                'price' => number_format((float) optional($plant)->gia, 2, '.', ''),
            ];
        })->sortBy('plant_id')->values()->all();

        return hash(
            'sha256',
            json_encode($items, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION)
        );
    }

    public function businessErrorMessage(string $code): string
    {
        if ($code === 'EMPTY_CART') {
            return 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi đặt hàng.';
        }

        if ($code === 'ORDER_CODE_USED') {
            return 'Mã đơn hàng vừa được sử dụng ở một phiên khác. Vui lòng tải lại trang thanh toán và quét QR mới.';
        }

        if ($code === 'CART_CHANGED') {
            return 'Giỏ hàng đã có thay đổi. Vui lòng kiểm tra lại trước khi đặt hàng.';
        }

        if (str_starts_with($code, 'PRODUCT_MISSING:')) {
            return 'Sản phẩm không còn tồn tại. Vui lòng cập nhật giỏ hàng.';
        }

        if (str_starts_with($code, 'PRODUCT_HIDDEN:')) {
            $name = mb_substr($code, mb_strlen('PRODUCT_HIDDEN:'));
            return "Sản phẩm {$name} hiện không khả dụng. Vui lòng cập nhật giỏ hàng.";
        }

        if (str_starts_with($code, 'OUT_OF_STOCK:')) {
            $name = mb_substr($code, mb_strlen('OUT_OF_STOCK:'));
            return "Sản phẩm {$name} hiện đã hết hàng. Vui lòng cập nhật giỏ hàng.";
        }

        if (str_starts_with($code, 'LOW_STOCK:')) {
            $payload = mb_substr($code, mb_strlen('LOW_STOCK:'));
            [$name, $stock] = array_pad(explode('|', $payload, 2), 2, '0');
            return "Sản phẩm {$name} chỉ còn {$stock} sản phẩm. Vui lòng cập nhật số lượng.";
        }

        return 'Không thể tạo đơn hàng. Vui lòng thử lại sau.';
    }
}
