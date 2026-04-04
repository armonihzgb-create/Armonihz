<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client; // <-- IMPORTANTE: Agregamos el modelo Client
use App\Models\MusicianProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\FirebaseService;

use App\Traits\ApiResponseTrait;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:cliente,musico',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($user->role === 'musico') {
            MusicianProfile::create([
                'user_id' => $user->id,
                'stage_name' => $user->name,
                'location' => null,
                'bio' => null,
                'is_verified' => false,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user->load('musicianProfile'),
            'token' => $token
        ], 'User registered successfully', 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', '=', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', null, 401);
        }

        if (!$user->is_active) {
            return $this->errorResponse('Account suspended.', null, 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user->load('musicianProfile'),
            'token' => $token
        ], 'Logged in successfully', 200);
    }

    public function firebaseLogin(Request $request, FirebaseService $firebaseService)
    {
        $request->validate([
            'firebase_token' => 'required|string',
        ]);

        try {
            $decodedToken = $firebaseService->verifyIdToken($request->firebase_token);
        }
        catch (\Throwable $e) {
            return $this->errorResponse('Invalid Firebase token', null, 401);
        }

        $email = $decodedToken->claims()->get('email');
        $name = $decodedToken->claims()->get('name');
        $uid = $decodedToken->claims()->get('sub');
        $picture = $decodedToken->claims()->get('picture'); // Obtenemos la foto por si acaso

        if (!$email) {
            return $this->errorResponse('Firebase token does not contain an email', null, 400);
        }

        // --- MAGIA AQUÍ: Usamos Client en lugar de User ---
        $client = Client::firstOrCreate(
            ['email' => $email],
            [
                'nombre' => $name ?? 'Usuario',
                'apellido' => '', // Por defecto vacío si no lo podemos separar
                'fotoPerfil' => $picture,
                'firebase_uid' => $uid,
            ]
        );

        // Actualizamos el UID de Firebase o foto si el cliente ya existía
        $changes = [];
        if (!$client->firebase_uid) {
            $changes['firebase_uid'] = $uid;
        }
        if ($client->fotoPerfil !== $picture && $picture !== null) {
            $changes['fotoPerfil'] = $picture;
        }
        
        if (!empty($changes)) {
            $client->update($changes);
        }

        // Creamos el token de Sanctum amarrado al modelo Client
        $token = $client->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'client' => $client,
            'token' => $token
        ], 'Firebase login successful', 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully', 200);
    }

    public function me(Request $request)
    {
        $authUser = $request->user();

        // Verificación de seguridad: si quien consulta es la App Móvil (Cliente)
        if ($authUser instanceof Client) {
            return $this->successResponse([
                'client' => $authUser
            ], 'Authenticated client retrieved', 200);
        }

        // Si quien consulta es un Músico (User)
        return $this->successResponse([
            'user' => $authUser->load('musicianProfile')
        ], 'Authenticated user retrieved', 200);
    }
}