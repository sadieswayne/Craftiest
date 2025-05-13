<?php

namespace App\Models\Voice;

use Illuminate\Database\Eloquent\Model;

class ElevenlabVoice extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'voice_id',
        'path',
        'status',
    ];
}
