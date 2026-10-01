<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()
                ->route('dang-nhap')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $nguoiDung = Auth::user();

        if (
            !$nguoiDung->vaiTro ||
            mb_strtolower(trim($nguoiDung->vaiTro->ten_vai_tro)) !== 'admin'
        ) {
            abort(403, 'Bạn không có quyền truy cập trang quản trị.');
        }

        return $next($request);
    }
}