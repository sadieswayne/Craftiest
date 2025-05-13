<?php

namespace App\Providers;

use App\Services\Bedrock\BedrockRuntimeService;
use Illuminate\Support\ServiceProvider;

class AwsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(BedrockRuntimeService::class, function () {
            return new BedrockRuntimeService([
                'region'      => config('filesystems.disks.s3.region'),
                'version'     => 'latest',
                'credentials' => [
                    'key'    => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
