<?php

namespace RachidLaasri\LaravelInstaller\Middleware;

use Closure;

class ApplicationCheckLicense
{
    public function handle($request, Closure $next)
    {
        return $next($request);

        // TODO: check license 6.4 version
        // return app(ExtensionRepositoryInterface::class)->check($request, $next);
    }
}
