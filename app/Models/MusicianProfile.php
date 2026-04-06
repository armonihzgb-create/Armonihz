<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicianProfile extends Model
{
    use HasFactory;

    // ── Valid verification states ────────────────────────────────────────────
    const STATUS_UNVERIFIED = 'unverified';
    const STATUS_PENDING    = 'pending';
    const STATUS_APPROVED   = 'approved';
    const STATUS_REJECTED   = 'rejected';

    /**
     * Auto-sync the legacy `is_verified` boolean whenever
     * `verification_status` is changed, so we never have to update
     * both fields manually in controllers.
     */
    protected static function booted(): void
    {
        static::saving(function (MusicianProfile $profile) {
            if ($profile->isDirty('verification_status')) {
                $profile->is_verified = $profile->verification_status === self::STATUS_APPROVED;
            }
        });
    }

    // ── Query Scopes ─────────────────────────────────────────────────────────
    public function scopePending($query)    { return $query->where('verification_status', self::STATUS_PENDING); }
    public function scopeApproved($query)   { return $query->where('verification_status', self::STATUS_APPROVED); }
    public function scopeRejected($query)   { return $query->where('verification_status', self::STATUS_REJECTED); }
    public function scopeUnverified($query) { return $query->where('verification_status', self::STATUS_UNVERIFIED); }
    public function scopeByStatus($query, string $status) { return $query->where('verification_status', $status); }

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
        'tiktok',
        'spotify',
        'coverage_notes',
        'verification_status',
        'id_document_path',
        'rejection_reason',
        'verified_at',
        'verified_by',
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

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function groupTypes()
    {
        return $this->belongsToMany(GroupType::class);
    }

    public function eventTypes()
    {
        return $this->belongsToMany(EventType::class);
    }
}
