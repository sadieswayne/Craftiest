<?php

namespace App\Observer\Frontend;

use App\Models\Frontend\FrontendSectionsStatus;
use Illuminate\Support\Facades\Cache;

class FrontendSectionsStatusObserver
{
    public function updated(FrontendSectionsStatus $frontendSectionsStatus): void
    {
        Cache::forget(FrontendSectionsStatus::$cacheKey);

        Cache::remember(FrontendSectionsStatus::$cacheKey, FrontendSectionsStatus::$cacheTtl, static function () use ($frontendSectionsStatus) {
            return $frontendSectionsStatus;
        });
    }
}
