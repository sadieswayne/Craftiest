<?php

namespace App\Http\Middleware\Custom;

use App\Helpers\Classes\Localization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->get('app_locale')) {
            app()->setLocale($request->get('app_locale'));

            Localization::setLocale($request->get('app_locale'));
        }

        $locale = Localization::getLocale();

        app()->setLocale($locale);

        return $next($request);
    }
}
