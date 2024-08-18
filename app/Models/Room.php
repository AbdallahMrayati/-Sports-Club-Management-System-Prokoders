<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['sport_id', 'name', 'capacity'];


    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }
}