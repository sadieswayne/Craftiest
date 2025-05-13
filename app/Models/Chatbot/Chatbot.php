<?php

namespace App\Models\Chatbot;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chatbot extends Model
{
    protected $table = 'chatbot';

    protected $fillable = [
        'user_id',
        'title',
        'role',
        'model',
        'first_message',
        'instructions',
        'chatbot_interests',
        'image',
        'width',
        'height',
        'color',
        'status',
    ];

    public function imageUrl(): Attribute
    {
        return new Attribute(
            get: fn () => asset('uploads/' . $this->image)
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function data(): HasMany
    {
        return $this->hasMany(ChatbotData::class, 'chatbot_id');
    }

    public function trainingData(): HasMany
    {
        return $this->hasMany(ChatbotData::class, 'chatbot_id', 'id')
            ->where('status', 'trained');
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function canNewDomainAdd(): bool
    {
        if ($this->domains->isEmpty()) {
            return true;
        }

        return $this->supportsMultipleDomains();
    }

    public function supportsMultipleDomains(): bool
    {
        return true; // @todo: implement this
    }
}
