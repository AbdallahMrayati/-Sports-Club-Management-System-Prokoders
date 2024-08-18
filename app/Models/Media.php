<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['sport_id', 'type', 'file_path'];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }
}