<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialMediaAccounts extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'key',
        'link',
        'icon',
        'is_active',
    ];
}
