<?php

namespace App\Http\Middleware\Custom;

use App\Helpers\Classes\InstallationHelper;
use App\Helpers\Classes\TableSchema;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class Migration74Middleware
{
    public function __construct(
    ) {}

    public function handle(Request $request, Closure $next): Response
    {

        $tables = app('magicai_tables');
        // and route not 'install' and not contain install
        if (! TableSchema::hasTable('roles', $tables) && $request->route()?->uri() !== 'install' && ! str_contains($request->route()?->uri(), 'install')) {
            Artisan::call('migrate', ['--force' => true]);

            InstallationHelper::runInstallation();
        }

        return $next($request);
    }
}
