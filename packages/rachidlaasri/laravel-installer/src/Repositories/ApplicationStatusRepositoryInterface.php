<?php

namespace RachidLaasri\LaravelInstaller\Repositories;

use Closure;
use Illuminate\Http\Request;

interface ApplicationStatusRepositoryInterface
{
    public function financePage(string $view = 'panel.admin.finance.gateways.particles.finance'): string;

    public function financeLicense(): bool;

    public function licenseType(): ?string;

    public function check(string $licenseKey, bool $installed = false): bool;

    public function portal();

    public function getVariable(string $key);

    public function generate(Request $request): bool;

    public function setLicense(): void;

    public function next($request, Closure $next);

    public function webhook($request);
}
