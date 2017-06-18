<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'text', 'published_at', 'published', 'views'];

    public function scopePublished($query)
    {
        return $query->where('published', true)->where('published_at', '<=', Carbon::now());
    }

    public function scopeLatest($query, $limit = 20)
    {
        return $query->published()->orderBy('published_at', 'desc')->limit($limit);
    }
}
