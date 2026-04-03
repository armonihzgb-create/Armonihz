<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', // Cambiado de user_id a client_id
        'musician_profile_id',
        'reason',
        'status'
    ];

    // Cambiamos el nombre de la relación para que sea más claro
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }
}