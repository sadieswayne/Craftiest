<?php

namespace App\Domains\Marketplace\Http\Middleware;

use App\Domains\Marketplace\Services\ExtensionInstallService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class NewExtensionInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = app(ExtensionInstallService::class)->getExtensionInstallCache();

        $check = Cache::get($key);

        if ($check) {
            Artisan::call('optimize:clear');

            Artisan::call('cache:clear');

            Artisan::call('migrate', ['--force' => true]);

            Artisan::call('vendor:publish', [
                '--tag'   => 'extension',
                '--force' => true,
            ]);

            Cache::forget($key);
        }

        return $next($request->merge([
            'credit-list-cache' => 'credit-list-cache-' . time(),
        ]));
    }
}
