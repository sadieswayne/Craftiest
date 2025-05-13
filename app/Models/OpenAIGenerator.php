<?php

namespace App\Models;

use App\Models\Concerns\HasCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenAIGenerator extends Model
{
    use HasCache;

    protected $table = 'openai';

    protected $guarded = [];

    public static string $cacheKey = 'cache_openai';

    public static int $cacheTtl = 3600 * 24;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
