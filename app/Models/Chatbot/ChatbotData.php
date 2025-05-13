<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Model;

class ChatbotData extends Model
{
    protected $fillable = [
        'chatbot_id',
        'content',
        'type',
        'type_value',
        'path',
        'status',
    ];
}
