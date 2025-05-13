<?php

namespace App\Http\Middleware\Custom;

use Closure;
use Illuminate\Http\Request;
use RachidLaasri\LaravelInstaller\Repositories\ApplicationStatusRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class FinanceLicenseMiddleware
{
    public function __construct(
        public ApplicationStatusRepositoryInterface $applicationStatusRepository,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->applicationStatusRepository->financeLicense()) {
            return $next($request);
        }

        return redirect()->route('dashboard.admin.license.index');
    }
}
