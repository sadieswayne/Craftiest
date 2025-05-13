<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSupport extends Model
{
    protected $table = 'user_support';

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(UserSupportMessage::class)->orderBy('created_at', 'asc');
    }

    public static function findByTicketId(string $ticketId): ?UserSupport
    {
        return static::query()->where('ticket_id', $ticketId)->first();
    }
}
