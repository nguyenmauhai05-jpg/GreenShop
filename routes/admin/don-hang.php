<?php

use App\Http\Controllers\Admin\DonHangController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin/don-hang')
    ->name('admin.don-hang.')
    ->group(function () {
        Route::get('/', [DonHangController::class, 'index'])->name('index');
        Route::get('/xuat-excel', [DonHangController::class, 'exportExcel'])->name('export-excel');
        Route::get('/xuat-pdf', [DonHangController::class, 'exportPdf'])->name('export-pdf');

        Route::post('/{donHang}/xac-nhan-don', [DonHangController::class, 'confirmCod'])
            ->whereNumber('donHang')->name('confirm-cod');

        Route::post('/{donHang}/cho-van-chuyen', [DonHangController::class, 'markPreparing'])
            ->whereNumber('donHang')->name('preparing');

        Route::post('/{donHang}/dang-giao', [DonHangController::class, 'markShipping'])
            ->whereNumber('donHang')->name('shipping');

        Route::post('/{donHang}/da-giao', [DonHangController::class, 'markDelivered'])
            ->whereNumber('donHang')->name('delivered');

    });
