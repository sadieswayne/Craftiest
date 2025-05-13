<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatMessage extends Model
{
    use HasFactory;

    protected $table = 'user_openai_chat_messages';

    protected $fillable = [
        'user_openai_chat_id',
        'user_id',
        'input',
        'response',
        'output',
        'hash',
        'credits',
        'words',
        'images',
        'pdfName',
        'pdfPath',
        'outputImage',
        'realtime',
        'is_chatbot',
    ];

    public function chat()
    {
        return $this->belongsTo(UserOpenaiChat::class, 'user_openai_chat_id', 'id');
    }
}
