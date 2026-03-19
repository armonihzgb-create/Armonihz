<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Relación con los eventos
    public function clientEvents()
    {
        // Un género puede estar en muchos eventos
        return $this->hasMany(ClientEvent::class, 'tipo_musica');
    }

    public function musicianProfiles()
    {
        return $this->belongsToMany(MusicianProfile::class , 'genre_musician_profile');
    }
}