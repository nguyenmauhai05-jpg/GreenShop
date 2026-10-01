<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class GreenShopSettings
{
    public static function defaults(): array
    {
        return [
            'store_name' => 'GreenShop',
            'contact_email' => 'contact@greenshop.com',
            'phone' => '0901234567',
            'address' => '123 Đường Lá Lát, Quận 1, TP. Hồ Chí Minh',
            'store_latitude' => null,
            'store_longitude' => null,
            'description' => 'GreenShop chuyên cung cấp các loại cây cảnh, cây nội thất, cây ngoại thất và phụ kiện.',
            'logo' => 'images/trang-chu/logo/logo.png',

            'currency' => 'VND',
            'language' => 'vi',
            'products_per_page' => 12,
            'date_format' => 'd/m/Y',

            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_from' => 'no-reply@greenshop.com',
            'mail_auth' => true,

            'maintenance_mode' => false,
        ];
    }

    public static function all(): array
    {
        $path = self::path();

        if (!File::exists($path)) {
            return self::defaults();
        }

        try {
            $decoded = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($decoded)) {
                return self::defaults();
            }

            return array_replace(self::defaults(), $decoded);
        } catch (\Throwable $e) {
            return self::defaults();
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::all();

        return array_key_exists($key, $settings)
            ? $settings[$key]
            : $default;
    }

    public static function save(array $settings): void
    {
        $directory = dirname(self::path());

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $payload = json_encode(
            array_replace(self::defaults(), $settings),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );

        $temp = self::path() . '.tmp';

        File::put($temp, $payload);
        File::move($temp, self::path());
    }

    public static function path(): string
    {
        return storage_path('app/greenshop-settings.json');
    }
}
