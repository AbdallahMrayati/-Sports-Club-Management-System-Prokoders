<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'sport_id' => $this->sport_id,
            'name' => $this->name,
            'capacity' => $this->capacity,
            // 'sport' => new SportResource($this->whenLoaded('sport')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}