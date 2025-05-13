<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class Testimonials extends Model
{
    protected $table = 'testimonials';

    protected $casts = [
        'words' => CleanHtml::class,
    ];
}
