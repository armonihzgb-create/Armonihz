<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientEvent extends Model
{
    use HasFactory, SoftDeletes;

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
        'status',
    ];

    public function applications()
    {
        return $this->hasMany(CastingApplication::class , 'client_event_id');
    }
    public function client()
{
    return $this->belongsTo(Client::class, 'firebase_uid', 'firebase_uid');
}

public function genre()
    {
        // 'tipo_musica' es la llave foránea en esta tabla (ClientEvent)
        // que apunta al 'id' de la tabla Genre
      return $this->belongsTo(Genre::class, 'tipo_musica');
    }
}