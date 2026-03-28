<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastingApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_event_id',
        'musician_profile_id',
        'proposed_price',
        'message',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(ClientEvent::class , 'client_event_id');
    }

    public function musician()
    {
        return $this->belongsTo(MusicianProfile::class , 'musician_profile_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
