<?php

namespace App\Services;

use App\Models\CuaHang;
use App\Models\DiaChi;
use App\Support\GreenShopSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MapShippingService
{
    public const FREE_DISTANCE_KM = 5.0;
    public const EXTRA_BLOCK_KM = 5.0;
    public const EXTRA_BLOCK_FEE = 20000;

    /**
     * Trả về báo giá vận chuyển + dữ liệu tuyến đường dùng cho bản đồ.
     */
    public function quoteForAddress(DiaChi $address): array
    {
        $store = $this->resolveStoreCoordinates();
        $customer = $this->resolveCustomerCoordinates($address);
        $route = $this->route($store['lat'], $store['lng'], $customer['lat'], $customer['lng']);

        $distanceKm = round((float) $route['distance_km'], 2);
        $shippingFee = $this->shippingFee($distanceKm);

        return [
            'distance_km' => $distanceKm,
            'shipping_fee' => $shippingFee,
            'rule' => [
                'free_km' => self::FREE_DISTANCE_KM,
                'block_km' => self::EXTRA_BLOCK_KM,
                'block_fee' => self::EXTRA_BLOCK_FEE,
            ],
            'store' => $store,
            'customer' => $customer,
            'route' => [
                'source' => $route['source'],
                'geometry' => $route['geometry'],
            ],
        ];
    }

    /**
     * Geocode một chuỗi địa chỉ bằng OpenStreetMap Nominatim.
     */
    public function geocode(string $address): array
    {
        $address = trim($address);
        if ($address === '') {
            throw new RuntimeException('Địa chỉ cần tìm không được để trống.');
        }

        $cacheKey = 'greenshop-geocode-' . sha1(mb_strtolower($address));

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($address) {
            $response = Http::withHeaders([
                'User-Agent' => 'GreenShop-Laravel/1.0',
                'Accept-Language' => 'vi,en;q=0.8',
            ])->acceptJson()
                ->timeout(10)
                ->retry(1, 300)
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $address,
                    'format' => 'jsonv2',
                    'limit' => 1,
                    'countrycodes' => 'vn',
                    'addressdetails' => 1,
                ]);

            if (!$response->successful()) {
                throw new RuntimeException('Không thể kết nối dịch vụ bản đồ để tìm địa chỉ.');
            }

            $item = $response->json('0');
            if (!is_array($item) || !isset($item['lat'], $item['lon'])) {
                throw new RuntimeException('Không tìm thấy vị trí tương ứng với địa chỉ đã nhập.');
            }

            return [
                'lat' => (float) $item['lat'],
                'lng' => (float) $item['lon'],
                'display_name' => (string) ($item['display_name'] ?? $address),
            ];
        });
    }


    /**
     * Reverse geocode tọa độ sang địa chỉ bằng Nominatim.
     * Gọi từ backend để tránh lỗi CORS/rate-limit ở trình duyệt.
     */
    public function reverseGeocode(float $latitude, float $longitude): array
    {
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            throw new RuntimeException('Tọa độ bản đồ không hợp lệ.');
        }

        $cacheKey = 'greenshop-reverse-geocode-' . sha1(
            round($latitude, 6) . '|' . round($longitude, 6)
        );

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($latitude, $longitude) {
            $response = Http::withHeaders([
                'User-Agent' => 'GreenShop-Laravel/1.0',
                'Accept-Language' => 'vi,en;q=0.8',
            ])->acceptJson()
                ->timeout(10)
                ->retry(1, 300)
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'jsonv2',
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'zoom' => 18,
                    'addressdetails' => 1,
                ]);

            if (!$response->successful()) {
                throw new RuntimeException('Không thể kết nối dịch vụ bản đồ để đọc địa chỉ.');
            }

            $item = $response->json();
            if (!is_array($item)) {
                throw new RuntimeException('Không đọc được địa chỉ từ vị trí đã chọn.');
            }

            $address = is_array($item['address'] ?? null) ? $item['address'] : [];

            $province = trim((string) (
                $address['city']
                ?? $address['state']
                ?? $address['province']
                ?? $address['municipality']
                ?? ''
            ));

            $ward = trim((string) (
                $address['ward']
                ?? $address['quarter']
                ?? $address['suburb']
                ?? $address['neighbourhood']
                ?? $address['village']
                ?? $address['town']
                ?? ''
            ));

            $streetParts = array_values(array_filter([
                $address['house_number'] ?? null,
                $address['road']
                    ?? $address['pedestrian']
                    ?? $address['residential']
                    ?? $address['path']
                    ?? null,
            ], fn ($value) => trim((string) $value) !== ''));

            $detail = trim(implode(' ', $streetParts));

            if ($detail === '') {
                $parts = array_values(array_filter(array_map(
                    'trim',
                    explode(',', (string) ($item['display_name'] ?? ''))
                )));

                $detail = implode(', ', array_slice($parts, 0, 2));
            }

            return [
                'lat' => (float) ($item['lat'] ?? $latitude),
                'lng' => (float) ($item['lon'] ?? $longitude),
                'display_name' => (string) ($item['display_name'] ?? ''),
                'tinh_thanh' => $province,
                'phuong_xa' => $ward,
                'dia_chi' => $detail,
            ];
        });
    }

    public function shippingFee(float $distanceKm): int
    {
        if ($distanceKm <= self::FREE_DISTANCE_KM) {
            return 0;
        }

        $extraDistance = $distanceKm - self::FREE_DISTANCE_KM;
        $blocks = (int) ceil($extraDistance / self::EXTRA_BLOCK_KM);

        return $blocks * self::EXTRA_BLOCK_FEE;
    }

    private function resolveStoreCoordinates(): array
    {
        $settings = GreenShopSettings::all();
        $lat = $this->nullableFloat($settings['store_latitude'] ?? null);
        $lng = $this->nullableFloat($settings['store_longitude'] ?? null);
        $addressText = trim((string) ($settings['address'] ?? ''));
        $name = trim((string) ($settings['store_name'] ?? 'GreenShop')) ?: 'GreenShop';

        if ($this->validCoordinates($lat, $lng)) {
            return [
                'lat' => $lat,
                'lng' => $lng,
                'name' => $name,
                'address' => $addressText,
            ];
        }

        // Tận dụng đúng bảng cua_hang đã có trong SQL nếu dữ liệu thực tế đang được lưu ở đây.
        $storeModel = CuaHang::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->first();

        if ($storeModel && $this->validCoordinates((float) $storeModel->latitude, (float) $storeModel->longitude)) {
            return [
                'lat' => (float) $storeModel->latitude,
                'lng' => (float) $storeModel->longitude,
                'name' => (string) ($storeModel->ten_cua_hang ?: $name),
                'address' => (string) ($storeModel->dia_chi ?: $addressText),
            ];
        }

        // Nếu Admin mới chỉ nhập địa chỉ chữ, tự geocode một lần và lưu tọa độ vào settings.
        if ($addressText !== '') {
            try {
                $point = $this->geocode($addressText . ', Việt Nam');
                $settings['store_latitude'] = $point['lat'];
                $settings['store_longitude'] = $point['lng'];
                GreenShopSettings::save($settings);

                return [
                    'lat' => $point['lat'],
                    'lng' => $point['lng'],
                    'name' => $name,
                    'address' => $addressText,
                ];
            } catch (\Throwable $e) {
                // Thử tiếp dữ liệu từ bảng cua_hang bên dưới.
            }
        }

        $storeModel = CuaHang::query()->whereNotNull('dia_chi')->first();
        if ($storeModel && trim((string) $storeModel->dia_chi) !== '') {
            $point = $this->geocode(trim((string) $storeModel->dia_chi) . ', Việt Nam');
            $storeModel->latitude = $point['lat'];
            $storeModel->longitude = $point['lng'];
            $storeModel->save();

            return [
                'lat' => $point['lat'],
                'lng' => $point['lng'],
                'name' => (string) ($storeModel->ten_cua_hang ?: $name),
                'address' => (string) $storeModel->dia_chi,
            ];
        }

        throw new RuntimeException('Chưa cấu hình vị trí cửa hàng. Admin hãy vào Cài đặt hệ thống, nhập địa chỉ cửa hàng và chọn vị trí trên bản đồ.');
    }

    private function resolveCustomerCoordinates(DiaChi $address): array
    {
        $lat = $this->nullableFloat($address->latitude);
        $lng = $this->nullableFloat($address->longitude);
        $fullAddress = $this->fullAddress($address);

        if ($this->validCoordinates($lat, $lng)) {
            return [
                'lat' => $lat,
                'lng' => $lng,
                'address' => $fullAddress,
            ];
        }

        $candidates = array_values(array_unique(array_filter([
            $fullAddress,
            collect([$address->phuong_xa, $address->tinh_thanh])->filter()->join(', '),
            collect([$address->tinh_thanh])->filter()->join(', '),
        ])));

        $lastError = null;
        foreach ($candidates as $candidate) {
            try {
                $point = $this->geocode($candidate . ', Việt Nam');
                $address->latitude = $point['lat'];
                $address->longitude = $point['lng'];
                $address->save();

                return [
                    'lat' => $point['lat'],
                    'lng' => $point['lng'],
                    'address' => $fullAddress,
                ];
            } catch (\Throwable $e) {
                $lastError = $e;
            }
        }

        throw new RuntimeException(
            $lastError?->getMessage() ?: 'Không xác định được tọa độ địa chỉ giao hàng. Vui lòng cập nhật địa chỉ chính xác hơn.'
        );
    }

    private function route(float $fromLat, float $fromLng, float $toLat, float $toLng): array
    {
        $cacheKey = 'greenshop-route-' . sha1(implode('|', [
            round($fromLat, 6), round($fromLng, 6), round($toLat, 6), round($toLng, 6),
        ]));

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($fromLat, $fromLng, $toLat, $toLng) {
            try {
                $url = sprintf(
                    'https://router.project-osrm.org/route/v1/driving/%F,%F;%F,%F',
                    $fromLng,
                    $fromLat,
                    $toLng,
                    $toLat
                );

                $response = Http::acceptJson()
                    ->timeout(10)
                    ->retry(1, 300)
                    ->get($url, [
                        'overview' => 'full',
                        'geometries' => 'geojson',
                        'steps' => 'false',
                    ]);

                if ($response->successful() && $response->json('code') === 'Ok') {
                    $route = $response->json('routes.0');
                    if (is_array($route) && isset($route['distance'])) {
                        return [
                            'distance_km' => ((float) $route['distance']) / 1000,
                            'geometry' => $route['geometry']['coordinates'] ?? null,
                            'source' => 'osrm',
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // Có fallback phía dưới để checkout vẫn dùng được khi OSRM tạm thời gián đoạn.
            }

            // Fallback khoảng cách đường chim bay. UI sẽ ghi rõ là ước tính.
            return [
                'distance_km' => $this->haversineKm($fromLat, $fromLng, $toLat, $toLng),
                'geometry' => [[$fromLng, $fromLat], [$toLng, $toLat]],
                'source' => 'haversine_fallback',
            ];
        });
    }

    private function fullAddress(DiaChi $address): string
    {
        return collect([
            $address->dia_chi,
            $address->phuong_xa,
            $address->tinh_thanh,
        ])->filter(fn ($value) => trim((string) $value) !== '')->join(', ');
    }

    private function validCoordinates(?float $lat, ?float $lng): bool
    {
        return $lat !== null && $lng !== null
            && $lat >= -90 && $lat <= 90
            && $lng >= -180 && $lng <= 180;
    }

    private function nullableFloat(mixed $value): ?float
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371.0088;
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;

        return $earthRadius * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}
