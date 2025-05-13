<?php

namespace App\Observer;

use App\Models\Ad;
use Illuminate\Support\Facades\Cache;

class AdObserver
{
    public function updated(Ad $ad): void
    {
        Cache::forget(Ad::$cacheKey);

        Ad::getCache(static function () {
            return Ad::query()->where('status', true)->get();
        });
    }
}
