<?php

namespace App\Observer;

use App\Models\Section\FeaturesMarquee;
use Illuminate\Support\Facades\Cache;

class FeaturesMarqueeObserver
{
    public function updated(FeaturesMarquee $featuresMarquee): void
    {
        Cache::forget(FeaturesMarquee::$cacheKey);

        FeaturesMarquee::getCache(static function () {
            return FeaturesMarquee::query()->select('title', 'position')->get();
        });
    }
}
