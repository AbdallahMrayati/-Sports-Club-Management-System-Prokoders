<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Relationship: Sport has many Rooms
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }


    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'sport_facilities');
    }

    public function days()
    {
        return $this->belongsToMany(Day::class, 'sport_day');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'sport_subscription');
    }
}