<?php

namespace App\Models;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticleCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
    ];
    public static $rules = [
        'name' => 'required|unique:article_categories,name',
    ];

    public function article()
    {
        return $this->hasMany(Article::class);
    }
}
