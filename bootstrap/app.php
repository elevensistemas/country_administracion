<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'force_change_password' => \App\Http\Middleware\ForceChangePasswordMiddleware::class,
        ]);
        
        $middleware->web(append: [
            \App\Http\Middleware\ForceChangePasswordMiddleware::class,
            \App\Http\Middleware\ThemeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle CSRF Token Mismatch / Page Expired (419) gracefully
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json([
                    'message' => 'Tu sesión ha expirado por inactividad. Por favor, recarga la página o ingresa nuevamente.',
                    'session_expired' => true,
                ], 419);
            }

            return redirect()->route('login')
                ->with('warning', 'Tu sesión ha expirado por inactividad. Por favor, ingresa nuevamente.');
        });

        // Also catch any HttpException with 419 status
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                    return response()->json([
                        'message' => 'Tu sesión ha expirado por inactividad. Por favor, recarga la página o ingresa nuevamente.',
                        'session_expired' => true,
                    ], 419);
                }

                return redirect()->route('login')
                    ->with('warning', 'Tu sesión ha expirado por inactividad. Por favor, ingresa nuevamente.');
            }
        });
    })->create();
