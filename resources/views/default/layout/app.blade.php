<!DOCTYPE html>
<html
    class="max-sm:overflow-x-hidden"
    lang="{{ LaravelLocalization::getCurrentLocale() }}"
    dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}"
>
<head>
	@if(setting('facebook_domain_verification'))
		<meta
			name="facebook-domain-verification"
			content="{{ setting('facebook_domain_verification') }}"
		/>
	@endif

	@if(setting('google_robots'))
		<meta name="robots" content="noindex, nofollow" />
	@endif

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
    <title>{{ getMetaTitle($setting, $settings_two) }}</title>

    @if (filled($google_fonts_string = \App\Helpers\Classes\ThemeHelper::googleFontsString()))
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

    @vite(\App\Helpers\Classes\ThemeHelper::appJsPath())

    @stack('css')

    @if (setting('additional_custom_css') != null)
        {!! setting('additional_custom_css') !!}
    @endif

    @livewireStyles
</head>

<body class="group/body bg-background font-body text-foreground">
    <div
        class="pointer-events-none invisible fixed left-0 right-0 top-0 z-[99] opacity-0 transition-opacity"
        id="app-loading-indicator"
        x-data="{}"
        :class="{ 'opacity-0': !$store.appLoadingIndicator.showing, 'invisible': !$store.appLoadingIndicator.showing }"
    >
        <div class="lqd-progress relative h-[3px] w-full bg-foreground/10">
            <div class="lqd-progress-bar lqd-progress-bar-indeterminate lqd-app-loading-indicator-progress-bar absolute inset-0 bg-primary dark:bg-heading-foreground">
            </div>
        </div>
    </div>

    @include('layout.header')

    @yield('content')

    @include('layout.footer')

    @if ($setting->frontend_custom_js != null)
        <script src="{{ $setting->frontend_custom_js }}"></script>
    @endif

    @if ($setting->frontend_code_before_body != null)
        {!! $setting->frontend_code_before_body !!}
    @endif

    <script src="{{ custom_theme_url('assets/libs/jquery/jquery.min.js') }}"></script>

    @if (in_array($settings_two->chatbot_status, ['frontend', 'both']))
        <script src="{{ custom_theme_url('assets/js/panel/openai_chatbot.js') }}"></script>
    @endif

    <script src="{{ custom_theme_url('assets/libs/vanillajs-scrollspy.min.js') }}"></script>
    <script src="{{ custom_theme_url('assets/libs/flickity.pkgd.min.js') }}"></script>
    <script src="{{ custom_theme_url('assets/js/frontend.js') }}"></script>
    <script src="{{ custom_theme_url('assets/js/frontend/frontend-animations.js') }}"></script>
    <script src="{{ custom_theme_url('assets/libs/vanillajs-scrollspy.min.js') }}"></script>
    <script src="{{ custom_theme_url('assets/libs/flickity.pkgd.min.js') }}"></script>
    <script src="{{ custom_theme_url('assets/js/frontend/frontend-animations.js') }}"></script>

    @if ($setting->gdpr_status == 1)
        <script src="{{ custom_theme_url('assets/js/gdpr.js') }}"></script>
    @endif

    <script src="{{ custom_theme_url('assets/libs/fslightbox/fslightbox.js') }}"></script>
    <script src="{{ custom_theme_url('assets/libs/toastr/toastr.min.js') }}"></script>

    @if (\Session::has('message'))
        <script>
            toastr.{{ \Session::get('type') }}('{{ \Session::get('message') }}')
        </script>
    @endif

    @livewireScriptConfig()

    @stack('script')
</body>

</html>
