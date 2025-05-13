<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'content',
        'feature_image',
        'slug',
        'seo_title',
        'seo_description',
        'categories',
        'tags',
    ];
}
