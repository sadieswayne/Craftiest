<?php

namespace App\Http\Middleware;

use Closure;
use Google2FA;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string[]  ...$guards
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        if (Auth::check()) {
            if (Google2FA::isActivated() && ! session()->has('save_login_2fa')) {
                $user = Auth::id();

                Auth::logout();

                session()->put('user_id', $user);
                session()->save();

                return redirect()->route('2fa.login');
            }
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {

            if ($request->routeIs('dashboard.user.openai.chat.*'));
        }

        return $request->expectsJson() ? null : route('login');
    }

    protected function unauthenticated($request, array $guards)
    {

        $text = $request->routeIs('dashboard.user.openai.chat.*') ? 'Please log in to your account to start using Live Chat.' : 'Unauthenticated.';

        throw new AuthenticationException(
            $text, $guards, $this->redirectTo($request)
        );
    }
}
