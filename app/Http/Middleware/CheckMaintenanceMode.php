<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Try/catch in case DB isn't ready
        try {
            if (Setting::get('maintenance_mode') == '1') {
                // If it's the login route, let them in so admins can login!
                if ($request->is('login') || $request->is('logout') || $request->is('admin*')) {
                    if (Auth::check() && Auth::user()->role === 'admin') {
                        return $next($request);
                    }
                    if ($request->is('login') && !Auth::check()) {
                        return $next($request); // Guests can access login page to authenticate as admin
                    }
                }

                // If logged in but not admin, or just a guest on a public page
                if (!Auth::check() || Auth::user()->role !== 'admin') {
                    abort(503, 'El sistema se encuentra en mantenimiento. Por favor vuelve pronto.');
                }
            }
        } catch (\Exception $e) {
            // If settings table doesn't exist yet, just continue
        }

        return $next($request);
    }
}
