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
                }
                );
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
                }
                );
            }),
        ];
    }
}
