<?php

namespace App\Helpers\Classes;

class Localization
{
    public static function getSupportedLocales(): array
    {
        return config('localization.supportedLocales');
    }

    public static function setLocale(string $value): void
    {
        session()->put('app_locale', $value);
        session()->save();
    }

    public static function getLocale(): ?string
    {
        return session()->get('app_locale') ?: app()->getLocale();
    }
}
