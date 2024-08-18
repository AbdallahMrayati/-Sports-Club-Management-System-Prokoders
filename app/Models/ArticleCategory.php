<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];


    public function article()
    {
        return $this->hasMany(Article::class);
    }
}