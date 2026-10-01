<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // No public payment callback is exempt from CSRF here.
        $middleware->validateCsrfTokens(except: ['thanh-toan/payos/webhook']);

        // Middleware Admin
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Nếu chưa đăng nhập thì chuyển về trang đăng nhập GreenShop
        $middleware->redirectGuestsTo(
            fn () => route('dang-nhap')
        );

        // Chế độ bảo trì do Admin bật/tắt trong Cài đặt hệ thống.
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SystemMaintenanceMode::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();