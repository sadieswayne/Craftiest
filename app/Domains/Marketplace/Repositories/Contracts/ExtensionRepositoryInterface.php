<?php

namespace App\Domains\Marketplace\Repositories\Contracts;

use Closure;

interface ExtensionRepositoryInterface extends PortalRepositoryInterface
{
    public function licensed(array $data);

    public function extensions(): array;

    public function banners(): ?array;

    public function paidExtensions(): array;

    public function themes(): array;

    public function subscription();

    public function all(bool $isTheme = false): array;

    public function find(string $slug);

    public function findId(int $id);

    public function findBySlugInDb(string $slug);

    public function install(string $slug, string $version);

    public function request(string $method, string $route, array $body = []);

    public function check($request, Closure $next);

    public function appVersion(): bool|string|int;

    public function cart(): ?array;

    public function blacklist(): bool;
}
