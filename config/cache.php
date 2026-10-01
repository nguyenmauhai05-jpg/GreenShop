<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | GreenShop Cache
    |--------------------------------------------------------------------------
    |
    | Cache mặc định lưu file. Project không yêu cầu bảng `cache`, Redis,
    | Memcached hay một database phụ.
    |
    */

    'default' => env('CACHE_STORE', 'file'),

    'stores' => [
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],
    ],

    'prefix' => env(
        'CACHE_PREFIX',
        Str::slug((string) env('APP_NAME', 'GreenShop')).'-cache-'
    ),

];
