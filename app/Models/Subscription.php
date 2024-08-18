<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'price', 'price_after', 'type'];

    public function sports()
    {
        return $this->belongsToMany(Sport::class, 'sport_subscription');
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'subscription_offer');
    }


    public function members()
    {
        return $this->belongsToMany(Member::class, 'subscription_member')
            ->withPivot('start_date', 'end_date', 'suspension_reason'); // Include pivot fields
    }
}