<?php

declare(strict_types=1);

namespace App\Models\Chatbot;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    use HasUuid;

    protected $fillable = [
        'domain',
        'app_key',
        'chatbot_id',
    ];

    public function chatbot(): BelongsTo
    {
        return $this->belongsTo(Chatbot::class);
    }

    public function scopeFindByPayload(Builder $query, string $domain, string $appKey): void
    {
        $query->where('domain', $domain)->where('app_key', $appKey);
    }

    public static function findByAppKey(string $appKey): ?Domain
    {
        return self::query()->where('app_key', $appKey)->first();
    }
}
