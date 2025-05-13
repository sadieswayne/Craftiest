<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class CustomSettings extends Model
{
    use HasFactory;

    protected $table = 'customsettings';

    protected $casts = [
        'value_html' => CleanHtml::class,
    ];
}
