<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'musician_profile_id',
        'reason',
        'status'
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id'); // O Client::class, según tu estructura
    }

    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }
}