<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureMusicianIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Si es músico, verificamos que su perfil esté aprobado
        if ($user && $user->role === 'musico') {
            $profile = $user->musicianProfile;

            // Si no tiene perfil aún, lo dejamos pasar por ahora (el código actual
            // asume que si tiene rol músico, podría o no tener perfil completo)
            // Pero si sí tiene perfil, checamos el status
            if ($profile && $profile->verification_status !== 'approved') {
                // Redirigir a la vista de información de verificación requerida
                return redirect()->route('id_verification.notice');
            }
        }

        return $next($request);
    }
}
