<?php

return [

    /*
    |--------------------------------------------------------------------------
    | GreenShop Queue
    |--------------------------------------------------------------------------
    |
    | GreenShop hiện xử lý đồng bộ và không dùng bảng jobs/job_batches/
    | failed_jobs. Giữ duy nhất driver sync để tránh phát sinh schema ngoài
    | Dump MySQL đang được quản lý bằng MySQL Workbench.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'sync'),

    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
    ],

    'failed' => [
        'driver' => 'null',
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
