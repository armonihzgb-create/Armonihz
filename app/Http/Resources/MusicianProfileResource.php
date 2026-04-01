<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MusicianProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stage_name' => $this->stage_name,
            'bio' => $this->bio,
            'location' => $this->location,
            'hourly_rate' => $this->hourly_rate,
            'is_verified' => $this->is_verified,
            'profile_picture' => $this->profile_picture,
            'phone' => $this->phone,
            'instagram' => $this->instagram,
            'facebook' => $this->facebook,
            'youtube' => $this->youtube,
            'coverage_notes' => $this->coverage_notes,

            'rating_average' => round($this->reviews_avg_rating ?? 0, 1),

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'genres' => $this->whenLoaded('genres', function () {
                return $this->genres->map(function ($genre) {
                    return [
                        'id' => $genre->id,
                        'name' => $genre->name,
                    ];
                });
            }),
            'promotions' => $this->whenLoaded('promotions', function () {
                return $this->promotions->map(function ($promo) {
                    return [
                        'id' => $promo->id,
                        'title' => $promo->title,
                        'description' => $promo->description,
                        'valid_from' => $promo->valid_from,
                        'valid_until' => $promo->valid_until,
                    ];
                });
            }),
            'media' => $this->whenLoaded('media', function () {
                $photos = $this->media->where('type', 'photo')->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'url' => $m->url(),
                    ];
                })->values();

                $videos = $this->media->where('type', 'video')->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'url' => $m->url(),
                        'title' => $m->title,
                        'is_featured' => $m->is_featured,
                    ];
                })->values();

                return [
                    'photos' => $photos,
                    'videos' => $videos,
                ];
            }),
            'is_favorite' => $this->is_favorite ?? false,
        ];
    }
}
