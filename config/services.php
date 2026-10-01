<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Map / Shipping (OpenStreetMap + OSRM)
    |--------------------------------------------------------------------------
    | Không cần API key cho môi trường demo/local. Tọa độ khách được cache vào
    | dia_chi.latitude/longitude, tọa độ cửa hàng được cache vào cua_hang.
    */
    'maps' => [
        // Khóa Google không cần thiết: không gọi Places/Geocoding/Directions API.
        'google_api_enabled' => env('MAP_GOOGLE_API_ENABLED', false),
        'osrm_url' => env('MAP_OSRM_URL', 'https://router.project-osrm.org'),
        'nominatim_url' => env('MAP_NOMINATIM_URL', 'https://nominatim.openstreetmap.org/search'),
        'timeout' => env('MAP_API_TIMEOUT', 8),
        'free_km' => env('SHIPPING_FREE_KM', 5),
        'block_km' => env('SHIPPING_BLOCK_KM', 5),
        'fee_per_block' => env('SHIPPING_FEE_PER_BLOCK', 20000),
    ],

    'payments' => [
        'demo_mode' => env('PAYMENT_DEMO_MODE', true),
        'ttl_minutes' => env('PAYMENT_PENDING_TTL_MINUTES', 15),
    ],


    'payos' => [
        'client_id' => env('PAYOS_CLIENT_ID', ''),
        'api_key' => env('PAYOS_API_KEY', ''),
        'checksum_key' => env('PAYOS_CHECKSUM_KEY', ''),
        'base_url' => env('PAYOS_BASE_URL', 'https://api-merchant.payos.vn'),
        'timeout' => env('PAYOS_TIMEOUT', 20),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID', ''),
        'secret' => env('PAYPAL_CLIENT_SECRET', ''),
        'base_url' => env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com'),
        'timeout' => env('PAYPAL_TIMEOUT', 30),
        'currency' => env('PAYPAL_CURRENCY', 'USD'),
        'vnd_per_usd' => env('PAYPAL_VND_PER_USD', 26000),
    ],

    // Provider ZaloPay được giữ để mở rộng payment gateway. Checkout hiện tại
    // chỉ hiển thị COD / PAYPAL / PAYOS, nên các biến này có thể để trống.
    'zalopay' => [
        'app_id' => env('ZALOPAY_APP_ID', 0),
        'key1' => env('ZALOPAY_KEY1', ''),
        'key2' => env('ZALOPAY_KEY2', ''),
        'base_url' => env('ZALOPAY_BASE_URL', 'https://sb-openapi.zalopay.vn'),
        'create_endpoint' => env('ZALOPAY_CREATE_ENDPOINT', '/v2/create'),
        'timeout' => env('ZALOPAY_TIMEOUT', 30),
        'expire_duration_seconds' => env('ZALOPAY_EXPIRE_DURATION_SECONDS', 900),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gemini AI
    |--------------------------------------------------------------------------
    */

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),

        'url' => env(
            'GEMINI_API_URL',
            'https://generativelanguage.googleapis.com/v1beta/models'
        ),

        'model' => env(
            'GEMINI_MODEL',
            'gemini-3-flash-preview'
        ),
    ],

];