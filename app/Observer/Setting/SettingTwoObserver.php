<?php

namespace App\Observer\Setting;

use App\Models\SettingTwo;
use Illuminate\Support\Facades\Cache;

class SettingTwoObserver
{
    public function updated(SettingTwo $model): void
    {
        Cache::delete(SettingTwo::$cacheKey);

        Cache::remember(SettingTwo::$cacheKey, 3600, static function () {
            return SettingTwo::query()->first();
        });
    }
}
