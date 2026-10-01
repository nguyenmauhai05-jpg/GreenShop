<?php

use App\Http\Controllers\Admin\CayCanhController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin/cay-canh')
    ->name('admin.cay-canh.')
    ->group(function () {
        Route::get('/', [CayCanhController::class, 'index'])->name('index');
        Route::get('/them', [CayCanhController::class, 'create'])->name('create');
        Route::post('/', [CayCanhController::class, 'store'])->name('store');
        Route::get('/xuat-excel', [CayCanhController::class, 'exportExcel'])->name('export-excel');

        Route::get('/{id}', [CayCanhController::class, 'show'])
            ->whereNumber('id')
            ->name('show');

        Route::get('/{id}/sua', [CayCanhController::class, 'edit'])
            ->whereNumber('id')
            ->name('edit');

        Route::put('/{id}', [CayCanhController::class, 'update'])
            ->whereNumber('id')
            ->name('update');

        Route::delete('/{id}', [CayCanhController::class, 'destroy'])
            ->whereNumber('id')
            ->name('destroy');
    });
