<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdateUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $expiresAt = now()->addMinutes(5);
            Cache::put('user-is-online-' . $userId, true, $expiresAt);
        }

        return $next($request);
    }
}
