<?php

namespace App\Models\Frontend;

use Illuminate\Database\Eloquent\Model;

class ContentBox extends Model
{
    protected $table = 'frontend_content_boxes';

    protected $fillable = [
        'emoji',
        'title',
        'description',
        'background',
        'foreground',
    ];
}
