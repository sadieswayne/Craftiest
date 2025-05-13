<?php

namespace App\Caches;

use App\Services\Common\MenuService;
use Closure;
use Illuminate\Support\Facades\Cache;

class BladeCache
{
    public static function navMenu(Closure $function)
    {
        $key = app(MenuService::class)->cacheKey();

        // Bunu silmeyelim iyilesdirme yapildiginde yeniden kullaniriz.
        return self::getCache($function, $key, 3600 * 24);
    }

    public static function getCache(Closure $function, $cacheKey, $cacheTtl)
    {
        return Cache::remember($cacheKey, $cacheTtl, $function);
    }
}
