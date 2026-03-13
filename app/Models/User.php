<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'firebase_uid', // Necesario para vincular con Firebase
        'google_id',
        'is_active',
        'is_verified',
        'email_verified_at',
        'fcm_token',    // Necesario para las notificaciones
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Send the branded email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    // RELACIONES
    public function musicianProfile()
    {
        return $this->hasOne(MusicianProfile::class);
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function clientRequests()
    {
        return $this->hasMany(HiringRequest::class , 'client_id');
    }

    /**
     * Canal de ruta para las notificaciones FCM.
     */
    public function routeNotificationForFcm($notification)
    {
        return $this->fcm_token;
    }
}