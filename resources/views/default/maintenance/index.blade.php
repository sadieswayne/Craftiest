@php
    $google_fonts_string = '';
    $theme_google_fonts = Theme::getSetting('landingPage.googleFonts');
    $app_js_path = 'resources/views/' . get_theme() . '/js/app.js';

    $i = 0;
    foreach ($theme_google_fonts as $font_name => $weights) {
        $font_string = 'family=' . str_replace(' ', '+', $font_name);
        if (!empty($weights)) {
            $font_string .= ':wght@' . implode(';', $weights);
        }
        $google_fonts_string .= $font_string . ($i === count($theme_google_fonts) - 1 ? '' : '&');
        $i++;
    }
@endphp

<!DOCTYPE html>
<html
        class="max-sm:overflow-x-hidden"
        lang="{{ LaravelLocalization::getCurrentLocale() }}"
        dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}"
>

<head>
    <meta charset="UTF-8" />
    <meta
            http-equiv="X-UA-Compatible"
            content="IE=edge"
    />
    <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
    />
    <meta
            name="description"
            content="{{ getMetaDesc($setting, $settings_two) }}"
    >
    @if (isset($setting->meta_keywords))
        <meta
                name="keywords"
                content="{{ $setting->meta_keywords }}"
        >
    @endif

    <link
            rel="icon"
            href="{{ custom_theme_url($setting->favicon_path ?? 'assets/favicon.ico') }}"
    >

    <title>
        {{ getMetaTitle($setting, $settings_two) }}
    </title>

    @if (filled($google_fonts_string))
        <link
                rel="preconnect"
                href="https://fonts.googleapis.com"
        >
        <link
                rel="preconnect"
                href="https://fonts.gstatic.com"
                crossorigin
        >
        <link
                href="https://fonts.googleapis.com/css2?{{ $google_fonts_string }}&display=swap"
                rel="stylesheet"
        >
    @endif

    <link
            rel="stylesheet"
            href="{{ custom_theme_url('assets/css/frontend/flickity.min.css') }}"
    >
    <link
            href="{{ custom_theme_url('assets/libs/toastr/toastr.min.css') }}"
            rel="stylesheet"
    />

    @php
        $link = 'resources/views/' . get_theme() . '/scss/landing-page.scss';
    @endphp
    @vite($link)

    @if ($setting->frontend_custom_css != null)
        <link
                rel="stylesheet"
                href="{{ $setting->frontend_custom_css }}"
        />
    @endif

    @if ($setting->frontend_code_before_head != null)
        {!! $setting->frontend_code_before_head !!}
    @endif

    <script>
        window.liquid = {
            isLandingPage: true
        };
    </script>

    <style>
        .google-ads-728 {
            width: 100%;
            max-width: 728px;
            height: auto;
        }
    </style>

    <!--Google AdSense-->
    {!! adsense_header() !!}
    <!--Google AdSense End-->

    {{-- Rewordfull start --}}
    {{-- <script>(function(w,r){w._rwq=r;w[r]=w[r]||function(){(w[r].q=w[r].q||[]).push(arguments)}})(window,'rewardful');</script> --}}
    {{-- <script async src='https://r.wdfl.co/rw.js' data-rewardful='API_KEY'></script> --}}
    {{-- Rewordfull end --}}

    @vite($app_js_path)

    @stack('css')

    @if (setting('additional_custom_css') != null)
        {!! setting('additional_custom_css') !!}
    @endif
</head>
<body class="d-flex justify-content-center align-center p-10">
<section class="bg-white  container mx-auto rounded-lg">
    <div class="py-8 px-4 mx-auto max-w-screen-md text-center lg:py-16 lg:px-12">
        <div class="flex justify-center">
            <a
                    class="site-logo relative mb-7"
                    href="{{ route('index') }}"
            >
                @if (isset($setting->logo_sticky))
                    <img
                            class="peer absolute start-0 top-1/2 -translate-y-1/2 translate-x-3 opacity-0 transition-all group-[.lqd-is-sticky]/header:translate-x-0 group-[.lqd-is-sticky]/header:opacity-100"
                            src="{{ custom_theme_url($setting->logo_sticky_path, true) }}"
                            @if (isset($setting->logo_sticky_2x_path)) srcset="/{{ $setting->logo_sticky_2x_path }} 2x" @endif
                            alt="{{ custom_theme_url($setting->site_name) }} logo"
                    >
                @endif
                <img
                        class="transition-all group-[.lqd-is-sticky]/header:peer-first:translate-x-2 group-[.lqd-is-sticky]/header:peer-first:opacity-0"
                        src="{{ custom_theme_url($setting->logo_path, true) }}"
                        @if (isset($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }} logo"
                >
            </a>
        </div>
        <h1 class="mb-4 text-4xl font-bold tracking-tight leading-none text-gray-900 lg:mb-6 md:text-5xl xl:text-6xl ">{{ data_get($data, 'header') }}</h1>
        <p class="font-light text-gray-500 md:text-lg xl:text-xl dark:text-gray-400">{{ data_get($data, 'paragraph') }}</p>
        <div class="p-5">
            <img src="{{ asset(data_get($data, 'image') ? 'uploads/'.data_get($data, 'image') :'images/maintenance/img.webp') }}">
        </div>
        <small class="font-light text-gray-500 dark:text-gray-400 mt-4">{!! data_get($data, 'footer') !!}</small>
    </div>
</section>
</body>
</html>
