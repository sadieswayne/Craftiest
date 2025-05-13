<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class Faq extends Model
{
    protected $table = 'faq';

    protected $casts = [
        'answer' => CleanHtml::class,
    ];
}
