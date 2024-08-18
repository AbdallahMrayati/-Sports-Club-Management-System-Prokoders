<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['articleCategory_id', 'title', 'content'];

    public function articleCategory()
    {
        return $this->belongsTo(ArticleCategory::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }
}