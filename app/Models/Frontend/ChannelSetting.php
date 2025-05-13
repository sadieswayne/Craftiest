<?php

namespace App\Models\Frontend;

use Illuminate\Database\Eloquent\Model;

class ChannelSetting extends Model
{
    protected $table = 'frontend_channel_settings';

    protected $fillable = [
        'title',
        'key',
        'description',
        'image',
        'logo',
    ];
}
