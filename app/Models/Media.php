<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;
    protected $fillable = [
        'article_id',
        'url',
    ];
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
