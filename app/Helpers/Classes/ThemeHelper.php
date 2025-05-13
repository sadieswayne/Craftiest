<?php

namespace App\Helpers\Classes;

use Igaster\LaravelTheme\Facades\Theme;

class ThemeHelper
{
    public static function googleFontsString(string $landingOrDash = 'landingPage'): string
    {
        $google_fonts_string = '';
        $theme_google_fonts = Theme::getSetting($landingOrDash . '.googleFonts');

        $i = 0;

        foreach ($theme_google_fonts ?? [] as $font_name => $weights) {
            $font_string = 'family=' . str_replace(' ', '+', $font_name);
            if (! empty($weights)) {
                $font_string .= ':wght@' . implode(';', $weights);
            }
            $google_fonts_string .= $font_string . ($i === count($theme_google_fonts) - 1 ? '' : '&');
            $i++;
        }

        return $google_fonts_string;
    }

    public static function dashboardScssPath(): string
    {
        return 'resources/views/' . self::getTheme() . '/scss/dashboard.scss';
    }

    public static function appJsPath(): string
    {
        return 'resources/views/' . self::getTheme() . '/js/app.js';
    }

    public static function getTheme(): string
    {
        return Theme::get() ?? 'default';
    }
}
