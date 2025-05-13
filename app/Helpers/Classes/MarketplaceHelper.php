<?php

namespace App\Helpers\Classes;

use App\Domains\Marketplace\MarketplaceServiceProvider;

class MarketplaceHelper
{
    public static function isRegistered(string $slug): bool
    {
        $providers = MarketplaceServiceProvider::getExtensionProviders();

        if (array_key_exists($slug, $providers)) {

            $loadedProviders = app()->getLoadedProviders();

            return array_key_exists($providers[$slug], $loadedProviders);
        }

        return false;
    }
}
