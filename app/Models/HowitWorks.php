<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class HowitWorks extends Model
{
    protected $table = 'howitworks';

    protected $guarded = [];

    protected $casts = [
        'title' => CleanHtml::class,
    ];
}
