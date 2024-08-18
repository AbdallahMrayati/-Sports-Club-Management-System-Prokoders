<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'status' => $this->status,
            'price' => $this->price,
            'price_after' => $this->price_after,
            'type' => $this->type,
            'sports' => $this->whenLoaded('sports', function () {
                return $this->sports->map(function ($sport) {
                    return [
                        'id' => $sport->id,
                        'name' => $sport->name,
                        'description' => $sport->description,
                    ];
                });
            }),
            'offers' => $this->whenLoaded('offers', function () {
                return $this->offers->map(function ($offer) {
                    return [
                        'id' => $offer->id,
                        'name' => $offer->name,
                        'description' => $offer->description,
                        'discount_percentage' => $offer->discount_percentage,
                        'start_date' => $offer->start_date,
                        'end_date' => $offer->end_date,
                    ];
                });
            }),
            'members' => $this->whenLoaded('members', function () {
                return $this->members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->name,
                        'email' => $member->email,
                        'phone' => $member->phone,
                    ];
                });
            }),
        ];
    }
}