<?php

namespace App\Observer;

use App\Models\OpenAIGenerator;
use Illuminate\Support\Facades\Cache;

class OpenAIGeneratorObserver
{
    public function updated(OpenAIGenerator $AIGenerator): void
    {
        Cache::forget(OpenAIGenerator::$cacheKey);

        OpenAIGenerator::getCache(function () {
            return OpenAIGenerator::query()->get();
        });
    }
}
