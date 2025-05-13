<?php

namespace App\Extensions\AiMusic\System\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMusic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'music_id',
        'audio_url',
        'image_url',
        'video_url',
        'duration',
        'status',
        'title',
    ];
}
