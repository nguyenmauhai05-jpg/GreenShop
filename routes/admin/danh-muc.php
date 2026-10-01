<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DanhMucController;

Route::middleware(['auth', 'admin'])
    ->prefix('admin/danh-muc')
    ->name('admin.danh-muc.')
    ->group(function () {

        // Danh sách
        Route::get(
            '/',
            [DanhMucController::class, 'index']
        )->name('index');

        // Xuất Excel
        Route::get(
            '/xuat-excel',
            [DanhMucController::class, 'exportExcel']
        )->name('export-excel');

        // Thêm danh mục
        Route::post(
            '/',
            [DanhMucController::class, 'store']
        )->name('store');

        // Cập nhật danh mục
        Route::put(
            '/{id}',
            [DanhMucController::class, 'update']
        )
        ->whereNumber('id')
        ->name('update');

        // Xóa danh mục
        Route::delete(
            '/{id}',
            [DanhMucController::class, 'destroy']
        )
        ->whereNumber('id')
        ->name('destroy');
    });