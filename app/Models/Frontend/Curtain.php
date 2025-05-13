<?php

namespace App\Models\Frontend;

use Illuminate\Database\Eloquent\Model;

class Curtain extends Model
{
    protected $table = 'frontend_curtains';

    protected $fillable = [
        'title',
        'title_icon',
        'sliders',
    ];

    protected $casts = [
        'sliders' => 'array',
    ];
}
