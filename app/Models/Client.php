<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'user_id',      // 🔥 ESTO FALTABA: Sin esto no se vincula con User
        'firebase_uid',
        'fotoPerfil',
        'nombre',
        'email'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}