<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                if (auth()->check()) {
                    return redirect()->route('dashboard')->with('info', 'Anda sudah masuk ke akun Anda.');
                }

                return redirect()->back()
                    ->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->with('error', 'Sesi formulir telah berakhir / kedaluwarsa. Halaman telah disegarkan dengan sesi baru, silakan kirim ulang formulir Anda.');
            }
        });
    })->create();
