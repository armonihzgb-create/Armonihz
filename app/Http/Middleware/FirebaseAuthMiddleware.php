<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
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

            // Guardamos datos para usar en controllers
            $request->attributes->add([
                'firebase_uid' => $uid,
                'firebase_email' => $email
            ]);

            /**
             * Crear cliente automáticamente si no existe
             */
            Client::updateOrCreate(
                ['firebase_uid' => $uid],
                [
                    'nombre' => $name,
                    'email' => $email
                ]
            );

        } catch (\Throwable $e) {

            return response()->json([
                'message' => 'Token inválido',
                'error' => $e->getMessage()
            ], 401);

        }

        return $next($request);
    }
}

