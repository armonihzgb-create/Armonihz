<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    /**
     * Auto-generate a URL-friendly slug whenever the name is created or changed.
     */
    protected static function booted(): void
    {
        static::saving(function (EventType $type) {
            if (empty($type->slug) || $type->isDirty('name')) {
                $baseSlug = Str::slug($type->name, '-');
                $slug     = $baseSlug;
                $counter  = 1;

                while (
                    static::where('slug', $slug)
                          ->when($type->exists, fn($q) => $q->where('id', '!=', $type->id))
                          ->exists()
                ) {
                    $slug = $baseSlug . '-' . $counter++;
                }

                $type->slug = $slug;
            }
        });
    }

    public function musicianProfiles()
    {
        return $this->belongsToMany(MusicianProfile::class);
    }
}
