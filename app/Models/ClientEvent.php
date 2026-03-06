<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEvent extends Model
{
    use HasFactory;

    // Los campos que se pueden llenar de forma masiva
    protected $fillable = [
        'firebase_uid',
        'titulo',
        'tipo_musica',
        'fecha',
        'duracion',
        'ubicacion',
        'descripcion',
        'presupuesto',
    ];
}