<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'expires_at' => $this->valid_until,
            'musician_profile' => $this->whenLoaded('musicianProfile', function () {
            return [
                    'id' => $this->musicianProfile->id,
                    'stage_name' => $this->musicianProfile->stage_name,
                ];
        }),
            'created_at' => $this->created_at,
        ];
    }
}
