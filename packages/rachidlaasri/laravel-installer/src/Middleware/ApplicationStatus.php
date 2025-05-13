<?php

namespace RachidLaasri\LaravelInstaller\Middleware;

use Closure;
use RachidLaasri\LaravelInstaller\Repositories\ApplicationStatusRepositoryInterface;

class ApplicationStatus
{
    public function handle($request, Closure $next)
    {
        return app(ApplicationStatusRepositoryInterface::class)->next($request, $next);
    }
}
