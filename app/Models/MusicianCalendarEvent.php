<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MusicianCalendarEvent extends Model
{
    protected $fillable = [
        'musician_profile_id',
        'title',
        'start',
        'end',
        'type',
        'color',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }
}
