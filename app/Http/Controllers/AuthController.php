<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        if (!$email) {
            return $this->errorResponse('Firebase token does not contain an email', null, 400);
        }

        $user = User::firstOrCreate(
        ['email' => $email],
        [
            'name' => $name ?? 'Usuario',
            'firebase_uid' => $uid,
            'role' => 'cliente',
            'password' => bcrypt(Str::random(16)),
        ]
        );

        // Update firebase_uid if the user already existed but logged in with Firebase now
        if (!$user->firebase_uid) {
            $user->update(['firebase_uid' => $uid]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user->load('musicianProfile'),
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
        return $this->successResponse([
            'user' => $request->user()->load('musicianProfile')
        ], 'Authenticated user retrieved', 200);
    }
}
