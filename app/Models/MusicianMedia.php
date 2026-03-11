<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicianMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'musician_profile_id',
        'type',
        'path',
        'title',
        'is_featured',
        'order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the absolute URL to the media file.
     */
    public function url(): string
    {
        return asset('storage/' . $this->path);
    }

    public function profile()
    {
        return $this->belongsTo(MusicianProfile::class, 'musician_profile_id');
    }
}
