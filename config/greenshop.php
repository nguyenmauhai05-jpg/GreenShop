<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Thông tin chuyển khoản GreenShop
    |--------------------------------------------------------------------------
    | Điền thông tin thật trong file .env. Mã QR VietQR ở trang thanh toán sẽ
    | tự lấy đúng số tiền cần thanh toán và nội dung chuyển khoản.
    */
    'bank_transfer' => [
        'bank_id' => env('GREENSHOP_BANK_ID', ''),
        'account_no' => env('GREENSHOP_BANK_ACCOUNT_NO', ''),
        'account_name' => env('GREENSHOP_BANK_ACCOUNT_NAME', 'GREENSHOP'),
        'template' => env('GREENSHOP_VIETQR_TEMPLATE', 'compact2'),
    ],
];
