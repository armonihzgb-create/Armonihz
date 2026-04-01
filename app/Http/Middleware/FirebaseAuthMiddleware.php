<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * FirebaseAuthMiddleware
 *
 * Delegates token verification to the FirebaseGuard registered in AppServiceProvider.
 * After this middleware runs, $request->user() returns the resolved Client model
 * throughout the entire request pipeline (controllers, policies, etc.).
 */
class FirebaseAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::guard('firebase');

        if (!$guard->check()) {
            return response()->json(['message' => 'Token inválido o no proporcionado'], 401);
        }

        // Since this is an API request authenticated via Firebase, we tell Laravel
        // to use the 'firebase' guard by default for the rest of the request lifecycle.
        Auth::shouldUse('firebase');

        // Bind the resolved client as the authenticated user so that
        // $request->user() and Auth::user() both return the Client model seamlessly.
        Auth::setUser($guard->user());

        return $next($request);
    }
}