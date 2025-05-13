<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Model;

class ChatbotDataVector extends Model
{
    protected $fillable = [
        'chatbot_id',
        'chatbot_data_id',
        'content',
        'embedding',
    ];

    protected $casts = [
        'embedding' => 'array',
    ];
}
