<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone_number', 'email', 'balance'];

    public function sports()
    {
        return $this->belongsToMany(Sport::class, 'member_sport');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'subscription_member')
            ->withPivot('start_date', 'end_date', 'suspension_reason'); // Include pivot fields if needed
    }
}