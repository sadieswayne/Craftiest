<?php

namespace App\Models\Section;

use Illuminate\Database\Eloquent\Model;

class FooterItem extends Model
{
    protected $table = 'footer_items';

    protected $fillable = [
        'item',
    ];
}
