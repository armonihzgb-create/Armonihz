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

    public function musicianProfiles()
    {
        return $this->belongsToMany(MusicianProfile::class , 'genre_musician_profile');
    }
}
