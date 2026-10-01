<?php

use App\Http\Controllers\Admin\DanhGiaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin/danh-gia')
    ->name('admin.danh-gia.')
    ->group(function () {
        Route::get('/', [DanhGiaController::class, 'index'])
            ->name('index');

        Route::post('/{danhGia}/tra-loi', [DanhGiaController::class, 'reply'])
            ->whereNumber('danhGia')
            ->name('reply');
    });
