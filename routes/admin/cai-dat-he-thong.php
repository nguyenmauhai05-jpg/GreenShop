<?php

use App\Http\Controllers\Admin\CaiDatHeThongController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin/cai-dat-he-thong')
    ->name('admin.cai-dat-he-thong.')
    ->group(function () {
        Route::get('/', [CaiDatHeThongController::class, 'index'])->name('index');
        Route::put('/', [CaiDatHeThongController::class, 'update'])->name('update');
    });
