<?php

namespace App\Observer\Setting;

use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use JetBrains\PhpStorm\NoReturn;

class SettingObserver
{
    #[NoReturn]
    public function updated(Setting $setting): void
    {
        Cache::forget(Setting::$cacheKey);

        $setting = Cache::remember(Setting::$cacheKey, Setting::$cacheTtl, static function () use ($setting) {
            return $setting;
        });

        Cache::forget(Setting::$cacheKey);

        Currency::cacheFromSetting($setting->getAttribute('default_currency'));
    }
}
