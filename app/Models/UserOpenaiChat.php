<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserOpenaiChat extends Model
{
    protected $table = 'user_openai_chat';

    protected $guarded = [];

    public function messages(): HasMany
    {
        return $this->hasMany(UserOpenaiChatMessage::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(OpenaiGeneratorChatCategory::class, 'openai_chat_category_id', 'id');
    }
}
