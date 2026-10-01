<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BaoCaoController;

Route::middleware(['auth', 'admin'])
    ->prefix('admin/bao-cao')
    ->name('admin.bao-cao.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | TRANG BÁO CÁO
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [BaoCaoController::class, 'index']
        )->name('index');


        /*
        |--------------------------------------------------------------------------
        | XUẤT EXCEL
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/xuat-excel',
            [BaoCaoController::class, 'exportExcel']
        )->name('export-excel');


        /*
        |--------------------------------------------------------------------------
        | XUẤT PDF
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/xuat-pdf',
            [BaoCaoController::class, 'exportPdf']
        )->name('export-pdf');

    });