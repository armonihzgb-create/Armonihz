<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MusicianProfile;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'user_id',     
        'firebase_uid',
        'fotoPerfil',
        'nombre',
        'email',
        'fcm_token'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
  public function favorites()
{
    // Relación de muchos a muchos apuntando a MusicianProfile
    return $this->belongsToMany(
        MusicianProfile::class, 
        'client_musician_favorites', 
        'client_id', 
        'musician_profile_id'
    )->withTimestamps();
}
}