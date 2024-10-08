<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'discount_percentage', 'start_date', 'end_date'];

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'subscription_offer');
    }
}