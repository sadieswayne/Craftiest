<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sentry\State\Scope;

use function Sentry\configureScope;

class SentryContextMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $currentUser = $request->user();

        if ($currentUser && app()->bound('sentry')) {
            configureScope(function (Scope $scope) use ($currentUser): void {
                $scope->setUser([
                    'id'       => $currentUser->id,
                    'name'     => $currentUser->name,
                    'surname'  => $currentUser->surname,
                    'type'     => $currentUser->type,
                    'email'    => $currentUser->email,
                ]);

                $scope->setContext('user_credits', $currentUser?->entity_credits ?? []);
            });
        }

        return $next($request);
    }
}
