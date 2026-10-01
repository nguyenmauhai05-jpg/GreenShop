<?php

namespace App\Http\Middleware;

use App\Support\GreenShopSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SystemMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!GreenShopSettings::get('maintenance_mode', false)) {
            return $next($request);
        }

        // Luôn cho phép đăng nhập/đăng xuất và khu vực Admin để Admin có thể tắt bảo trì.
        if (
            $request->is('admin')
            || $request->is('admin/*')
            || $request->is('dang-nhap')
            || $request->is('dang-xuat')
            || $request->is('up')
        ) {
            return $next($request);
        }

        return response()->view('errors.503', [
            'storeName' => GreenShopSettings::get('store_name', 'GreenShop'),
        ], 503);
    }
}
