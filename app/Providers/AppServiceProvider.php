<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Auth\FirebaseGuard;
use App\Services\FirebaseService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Firebase Auth Guard registration ─────────────────────────────────
        // Makes Auth::guard('firebase') and $request->user() resolve to the
        // Client model on all routes protected by the 'firebase' guard.
        Auth::extend('firebase', function ($app, $name, array $config) {
            return new FirebaseGuard(
                $app['request'],
                $app->make(FirebaseService::class)
            );
        });

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip() . '|' . $request->input('email'));
        });

        RateLimiter::for ('public-api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for ('hiring', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()->id);
        });

        RateLimiter::for ('promotions', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()->id);
        });

        // Compartir el conteo de músicos pendientes con el layout de admin
        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {
            $pendingCount = \App\Models\MusicianProfile::where('verification_status', 'pending')->count();
            $view->with('pendingMusiciansCountSidebar', $pendingCount);
        });
    }
}
