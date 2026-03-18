<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use App\Models\User;
use App\Models\Client;

class FirebaseAuthMiddleware
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'Token no proporcionado'
            ], 401);
        }

        $idToken = substr($authHeader, 7);

        try {

            $decodedToken = $this->firebaseService->verifyIdToken($idToken);

            $uid = $decodedToken->claims()->get('sub');
            $email = $decodedToken->claims()->get('email');
            $name = $decodedToken->claims()->get('name') ?? 'Usuario';

            // Guardar en request para usar en controllers
            $request->attributes->add([
                'firebase_uid' => $uid,
                'firebase_email' => $email
            ]);

            /**
             * 1️⃣ Buscar o crear usuario en tabla users
             */
            $user = User::updateOrCreate(
                ['firebase_uid' => $uid],
                [
                    'email' => $email,
                    'name' => $name,
                    'role' => 'cliente'
                ]
            );

            /**
             * 2️⃣ Crear cliente si no existe
             */
            Client::updateOrCreate(
                ['firebase_uid' => $uid],
                [
                    'user_id' => $user->id,
                    'nombre' => $user->name,
                    'email' => $user->email
                ]
            );

        } catch (\Throwable $e) {

            return response()->json([
                'message' => 'Token inválido',
                'error' => $e->getMessage()
            ], 401);

        }

        \Log::info('FIREBASE UID:', [$request->attributes->get('firebase_uid')]);

        return $next($request);
    }
}

