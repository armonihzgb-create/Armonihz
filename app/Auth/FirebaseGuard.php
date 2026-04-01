<?php

namespace App\Auth;

use App\Models\Client;
use App\Services\FirebaseService;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

/**
 * FirebaseGuard
 *
 * Custom Laravel Auth Guard for Firebase-authenticated API routes.
 * It verifies the Bearer token, resolves (or auto-creates) the Client
 * record, and stores it so $request->user() returns the Client model
 * transparently — no changes needed in controllers.
 */
class FirebaseGuard implements Guard
{
    protected Request $request;
    protected FirebaseService $firebase;
    protected ?Client $resolvedUser = null;
    protected bool $attempted = false;

    public function __construct(Request $request, FirebaseService $firebase)
    {
        $this->request  = $request;
        $this->firebase = $firebase;
    }

    // ── Guard interface ────────────────────────────────────────────

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function user(): ?Client
    {
        // Only attempt token verification once per request lifecycle
        if ($this->attempted) {
            return $this->resolvedUser;
        }

        $this->attempted = true;

        $authHeader = $this->request->header('Authorization', '');

        if (!str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $idToken = substr($authHeader, 7);

        try {
            $decoded = $this->firebase->verifyIdToken($idToken);

            $uid   = $decoded->claims()->get('sub');
            $email = $decoded->claims()->get('email');
            $name  = $decoded->claims()->get('name') ?? 'Usuario';

            // Inject attributes for controllers that still read them directly
            $this->request->attributes->set('firebase_uid',   $uid);
            $this->request->attributes->set('firebase_email', $email);

            // Resolve or auto-create the Client record
            $client = Client::firstOrCreate(
                ['firebase_uid' => $uid],
                [
                    'nombre'   => explode(' ', trim($name), 2)[0] ?? '',
                    'apellido' => explode(' ', trim($name), 2)[1] ?? '',
                    'email'    => $email,
                ]
            );

            $this->resolvedUser = $client;

        } catch (\Throwable) {
            $this->resolvedUser = null;
        }

        return $this->resolvedUser;
    }

    public function id(): int|string|null
    {
        return $this->user()?->id;
    }

    public function validate(array $credentials = []): bool
    {
        return false; // Token-based auth; validation not applicable
    }

    public function hasUser(): bool
    {
        return $this->resolvedUser !== null;
    }

    public function setUser(Authenticatable $user): static
    {
        // The Guard contract requires Authenticatable; we cast to Client since
        // this guard exclusively resolves Client models from Firebase tokens.
        /** @var Client $user */
        $this->resolvedUser = $user;
        return $this;
    }
}
