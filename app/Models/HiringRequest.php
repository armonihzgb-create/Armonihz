<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HiringRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'musician_profile_id',
        'event_date',
        'event_location',
        'description',
        'budget',
        'status',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class , 'client_id');
    }

    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }
}
