<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'          => \App\Http\Middleware\EnsureUserHasRole::class,
            'active'        => \App\Http\Middleware\CheckUserIsActive::class,
            'firebase.auth' => \App\Http\Middleware\FirebaseAuthMiddleware::class,
            'verified_musician' => \App\Http\Middleware\EnsureMusicianIsVerified::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\CheckUserIsActive::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'auth/google/callback',
            'app-registro',
            'app-registro2',
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please slow down.',
                    'data'    => null,
                    'errors'  => null,
                ], 429);
            }
        });
    })->create();
