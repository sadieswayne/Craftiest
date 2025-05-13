<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class BadWord extends Model
{
    protected $fillable = [
        'user_id',
        'words',
        'language',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getWordsAsCollection(): Collection
    {
        return str($this->words)->explode(',')
            ->map(fn ($word) => trim(strtolower($word)))
            ->filter(fn ($word) => strlen($word) > 0);
    }

    public function getWordsAsArray(): array
    {
        return $this->getWordsAsCollection()->toArray();
    }
}
