<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'body', 'category_id', 'slug', 'excerpt'
    ];


    public function article_categories()
    {
        return $this->belongsTo(ArticleCategory::class);
    }
    public function media()
    {
        return $this->hasMany(Media::class);
    }
}
