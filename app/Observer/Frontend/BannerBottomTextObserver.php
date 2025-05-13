<?php

namespace App\Observer\Frontend;

use App\Models\Section\BannerBottomText;
use Illuminate\Support\Facades\Cache;

class BannerBottomTextObserver
{
    public function updated(BannerBottomText $bannerBottomText): void
    {
        Cache::forget(BannerBottomText::$cacheKey);

        BannerBottomText::getCache(static function () {
            return BannerBottomText::query()->select('text')->pluck('text')->toArray();
        });
    }
}
