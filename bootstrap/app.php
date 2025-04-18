<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware; // Thay thế bằng middleware của bạn
use App\Http\Middleware\CorsMiddleware; // Import middleware vừa tạo
use Spatie\Permission\Middleware\RoleMiddleware;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (\Illuminate\Foundation\Configuration\Middleware $middleware) {
        $middleware->alias([
            'cors'  => CorsMiddleware::class,
            'admin' => AdminMiddleware::class, // middleware custom của bạn
            'role'  => RoleMiddleware::class,  // đăng ký alias cho RoleMiddleware
        ]);
    })
    ->withExceptions(function (\Illuminate\Foundation\Configuration\Exceptions $exceptions) {
        //
    })->create();
