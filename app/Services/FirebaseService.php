<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class FirebaseService
{
    protected Auth $auth;

    public function __construct()
    {
        $credentials = config('firebase.credentials');

        if (!$credentials || !file_exists($credentials)) {
            throw new \Exception('Firebase credentials file not found');
        }

        $factory = (new Factory)
            ->withServiceAccount($credentials);

        $this->auth = $factory->createAuth();
    }

    public function verifyIdToken(string $idToken)
    {
        try {
            return $this->auth->verifyIdToken($idToken);
        } catch (FailedToVerifyToken $e) {
            throw new \Exception('Invalid Firebase token');
        }

        
    }
    // 👇 AGREGA ESTO AQUÍ 👇
    public function getAuth(): Auth
    {
        return $this->auth;
    }
}