<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SportResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'media' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'type' => $media->type,
                        'file_path' => $media->file_path,
                    ];
                });
            }),
            'facilities' => $this->whenLoaded('facilities', function () {
                return $this->facilities->map(function ($facility) {
                    return [
                        'id' => $facility->id,
                        'name' => $facility->name,
                        'description' => $facility->description,
                    ];
                });
            }),
            'days' => $this->whenLoaded('days', function () {
                return $this->days->map(function ($day) {
                    return [
                        'id' => $day->id,
                        'name' => $day->name,
                    ];
                });
            }),
            'rooms' => $this->whenLoaded('rooms', function () {
                return $this->rooms->map(function ($room) {
                    return [
                        'id' => $room->id,
                        'name' => $room->name,
                    ];
                });
            }),
            'subscriptions' => $this->whenLoaded('subscriptions', function () {
                return $this->subscriptions->map(function ($subscription) {
                    return [
                        'id' => $subscription->id,
                        'name' => $subscription->name,
                        'description' => $subscription->description,
                    ];
                });
            }),
        ];
    }
}