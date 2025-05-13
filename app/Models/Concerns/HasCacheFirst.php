<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Cache;

trait HasCacheFirst
{
    public static function getCache()
    {
        return Cache::remember(self::$cacheKey, self::$cacheTtl, static function () {
            return self::query()->first();
        });
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::$cacheKey);
    }
}
