@extends('panel.layout.app', ['layout_wide' => true, 'wide_layout_px' => 'px-0'])

@section('content')
    <header class="absolute left-0 right-0 top-0 flex items-center px-8 pt-8 max-lg:px-1">
        <div class="flex-grow">
            <a
                class="navbar-brand"
                href="{{ route('index') }}"
            >
                @if (isset($setting->logo_dashboard))
                    <img
                        class="group-[.navbar-shrinked]/body:hidden dark:hidden"
                        src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                        @if (isset($setting->logo_dashboard_2x_path) && !empty($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                    <img
                        class="hidden group-[.navbar-shrinked]/body:hidden dark:block"
                        src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}"
                        @if (isset($setting->logo_dashboard_dark_2x_path) && !empty($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                @else
                    <img
                        class="group-[.navbar-shrinked]/body:hidden dark:hidden"
                        src="{{ custom_theme_url($setting->logo_path, true) }}"
                        @if (isset($setting->logo_2x_path) && !empty($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                    <img
                        class="hidden group-[.navbar-shrinked]/body:hidden dark:block"
                        src="{{ custom_theme_url($setting->logo_dark_path, true) }}"
                        @if (isset($setting->logo_dark_2x_path) && !empty($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                @endif
            </a>
        </div>
        <div class="flex-grow text-end">
            <a
                class="inline-flex items-center gap-1 text-heading-foreground no-underline hover:underline lg:text-white"
                href="{{ route('index') }}"
            >
                <x-tabler-chevron-left class="w-4" />
                {{ __('Back to Home') }}
            </a>
        </div>
    </header>

    <div class="lqd-auth-content flex min-h-screen w-full flex-wrap items-stretch max-md:pb-20 max-md:pt-32">
        <div class="grow md:flex md:w-1/2 md:flex-col md:items-center md:justify-center md:py-20">
            <div class="w-full px-4 text-center text-2xs lg:w-1/2">
                @yield('form')
            </div>
        </div>

        @if (
            $setting->auth_view_options != null &&
                $setting->auth_view_options != 'undefined' &&
                json_decode($setting->auth_view_options)?->login_enabled == true &&
                json_decode($setting->auth_view_options)?->login_image != null &&
                json_decode($setting->auth_view_options)?->login_image != '')
            <div
                class="hidden flex-col justify-center overflow-hidden bg-cover bg-center md:flex md:w-1/2"
                style="background-image: url({{ json_decode($setting->auth_view_options)->login_image }})"
            >
            @else
                <div
                    class="hidden flex-col justify-center overflow-hidden bg-cover bg-center md:flex md:w-1/2"
                    style="background-image: url({{ custom_theme_url('/images/bg/bg-auth.jpg') }})"
                >
                    <img
                        class="translate-x-[27%] rounded-[36px] shadow-[0_24px_88px_rgba(0,0,0,0.55)]"
                        src="{{ custom_theme_url('/images/bg/dash-mockup.jpg') }}"
                        alt="MagicAI Dashboard Mockup"
                    >
                </div>
        @endif
    </div>
@endsection
