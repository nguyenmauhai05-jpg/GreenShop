<?php

namespace App\Services\Checkout;

use App\Services\MapShippingService;

class ShippingPricingService
{
    public function feeForMethod(string $method, float $distanceKm, MapShippingService $map): float
    {
        $distanceKm = max(0, $distanceKm);

        return match ($method) {
            'EXPRESS' => (float) $map->shippingFee($distanceKm),
            'GHTK_EXPRESS' => $distanceKm <= 5
                ? 18000
                : ($distanceKm <= 10
                    ? 25000
                    : min(60000, 25000 + (ceil(($distanceKm - 10) / 10) * 7000))),
            default => $distanceKm <= 5
                ? 15000
                : ($distanceKm <= 10
                    ? 20000
                    : ($distanceKm <= 20
                        ? 30000
                        : min(50000, 30000 + (ceil(($distanceKm - 20) / 10) * 5000)))),
        };
    }

    public function isHanoiAddress(object $address): bool
    {
        $province = mb_strtolower(trim((string) ($address->tinh_thanh ?? '')), 'UTF-8');

        $province = str_replace(
            [
                'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
                'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
                'ì','í','ị','ỉ','ĩ','ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
                'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ','ỳ','ý','ỵ','ỷ','ỹ','đ',
            ],
            [
                'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
                'e','e','e','e','e','e','e','e','e','e','e',
                'i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
                'u','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y','d',
            ],
            $province
        );

        return str_contains($province, 'ha noi') || str_contains($province, 'hanoi');
    }
}
