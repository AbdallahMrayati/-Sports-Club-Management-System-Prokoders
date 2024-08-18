<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'sports' => $this->whenLoaded('sports', function () {
                return $this->sports->map(function ($sport) {
                    return [
                        'id' => $sport->id,
                        'name' => $sport->name,
                        'description' => $sport->description,
                    ];
                });
            }),
            'subscriptions' => $this->whenLoaded('subscriptions', function () {
                return $this->subscriptions->map(function ($subscription) {
                    return [
                        'id' => $subscription->id,
                        'start_date' => $subscription->pivot->start_date,
                        'end_date' => $subscription->pivot->end_date,
                        'suspension_reason' => $subscription->pivot->suspension_reason,
                        'price' => $subscription->price,
                        'type' => $subscription->type,
                    ];
                });
            }),
        ];
    }
}