<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'musician_profile_id',
        'title',
        'description',
        'valid_from',
        'valid_until',
        'is_active',
        'views',
        // --- Nuevos campos agregados para el flujo de pagos ---
        'status',
        'receipt_path',
        'plan_type',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }
}