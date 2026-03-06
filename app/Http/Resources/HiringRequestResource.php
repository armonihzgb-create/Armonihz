<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HiringRequestResource extends JsonResource
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
            'event_date' => $this->event_date,
            'event_location' => $this->event_location,
            'description' => $this->description,
            'budget' => $this->budget,
            'status' => $this->status,
            'client' => $this->whenLoaded('client', function () {
            return [
                    'id' => $this->client->id,
                    'name' => $this->client->name,
                ];
        }),
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
