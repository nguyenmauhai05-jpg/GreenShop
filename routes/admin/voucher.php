<?php

use App\Http\Controllers\Admin\VoucherController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin/voucher')
    ->name('admin.voucher.')
    ->group(function () {
        Route::get('/', [VoucherController::class, 'index'])->name('index');
        Route::post('/', [VoucherController::class, 'store'])->name('store');
        Route::put('/{voucher}', [VoucherController::class, 'update'])
            ->whereNumber('voucher')
            ->name('update');
        Route::patch('/{voucher}/trang-thai', [VoucherController::class, 'toggle'])
            ->whereNumber('voucher')
            ->name('toggle');
        Route::delete('/{voucher}', [VoucherController::class, 'destroy'])
            ->whereNumber('voucher')
            ->name('destroy');
    });
