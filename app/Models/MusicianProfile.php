<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicianProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stage_name',
        'bio',
        'location',
        'hourly_rate',
        'profile_picture',
        'is_verified',
        'phone',
        'instagram',
        'facebook',
        'youtube',
        'coverage_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class , 'genre_musician_profile');
    }

    public function hiringRequests()
    {
        return $this->hasMany(HiringRequest::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    public function castingApplications()
    {
        return $this->hasMany(CastingApplication::class , 'musician_profile_id');
    }

    public function media()
    {
        return $this->hasMany(MusicianMedia::class);
    }

    public function profilePictureUrl()
    {
        if (!$this->profile_picture) {
            return asset('images/default-avatar.png'); // Si tienes un avatar por defecto
        }

        if (filter_var($this->profile_picture, FILTER_VALIDATE_URL)) {
            return $this->profile_picture;
        }

        // Force to use our custom file streaming route, bypassing EasyPanel symlink issues
        return url('file/' . $this->profile_picture);
    }

    public function calendarEvents()
    {
        return $this->hasMany(MusicianCalendarEvent::class);
    }
}
