<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

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
            return response()->json(['message' => 'Token no proporcionado'], 401);
        }

        $idToken = substr($authHeader, 7);

        try {
            $decodedToken = $this->firebaseService->verifyIdToken($idToken);

            $request->attributes->add([
                'firebase_uid' => $decodedToken->claims()->get('sub'),
                'firebase_email' => $decodedToken->claims()->get('email'),
            ]);

        } catch (\Throwable $e) {
    return response()->json([
        'message' => 'Token inválido',
        'error' => $e->getMessage()
    ], 401);
}

        

         \Log::info('AUTH HEADER:', [$request->header('Authorization')]);
    \Log::info('FIREBASE UID:', [$request->attributes->get('firebase_uid')]);
        return $next($request);

    }
    
    
}