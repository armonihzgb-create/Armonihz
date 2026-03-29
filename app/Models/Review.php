<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'client_id',
        'musician_profile_id',
        'hiring_request_id',
        'casting_application_id',
        'rating',
        'comment',
        'response'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }

    public function hiringRequest()
    {
        return $this->belongsTo(HiringRequest::class);
    }

    public function castingApplication()
    {
        return $this->belongsTo(CastingApplication::class);
    }
}
