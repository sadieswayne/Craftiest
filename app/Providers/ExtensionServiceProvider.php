<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ExtensionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $providers = $this->extensionProviders();

        foreach ($providers as $provider) {
            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }

    private function extensionProviders(): array
    {
        return [
            \App\Providers\ChatbotServiceProvider::class,
        ];
    }
}
