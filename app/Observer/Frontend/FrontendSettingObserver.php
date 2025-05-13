<?php

namespace App\Observer\Frontend;

use App\Models\Frontend\FrontendSetting;
use Illuminate\Support\Facades\Cache;

class FrontendSettingObserver
{
    public function updated(FrontendSetting $frontendSetting): void
    {
        Cache::forget(FrontendSetting::$cacheKey);

        Cache::remember(FrontendSetting::$cacheKey, FrontendSetting::$cacheTtl, static function () use ($frontendSetting) {
            return $frontendSetting;
        });
    }
}
