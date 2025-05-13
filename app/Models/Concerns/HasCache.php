<?php

namespace App\Models\Concerns;

use Closure;
use Illuminate\Support\Facades\Cache;

trait HasCache
{
    public static function getCache(Closure $function, string $suffix = '')
    {
        return Cache::remember(self::$cacheKey . $suffix, self::$cacheTtl, $function);
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::$cacheKey);
    }
}
