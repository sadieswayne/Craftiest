<?php

namespace App\Models\Common;

use App\Enums\Common\MenuGroupTypeEnum;
use Illuminate\Database\Eloquent\Model;

class MenuGroup extends Model
{
    protected $fillable = [
        'name',
        'type',
        'status',
    ];

    protected $casts = [
        'type' => MenuGroupTypeEnum::class,
    ];
}
