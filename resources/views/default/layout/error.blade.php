@extends('panel.layout.app', ['disable_tblr' => true])

@section('content')
    <header class="fixed inset-x-0 top-0 flex justify-center border-b bg-background/10 px-4 py-6 backdrop-blur-md">
        <figure>
            @if (isset($setting->logo_dashboard))
                <img
                    class="max-h-14 w-auto dark:hidden"
                    src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                    @if (isset($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
                <img
                    class="hidden max-h-14 w-auto dark:block"
                    src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}"
                    @if (isset($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
            @else
                <img
                    class="max-h-14 w-auto dark:hidden"
                    src="{{ custom_theme_url($setting->logo_path, true) }}"
                    @if (isset($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
                <img
                    class="hidden max-h-14 w-auto dark:block"
                    src="{{ custom_theme_url($setting->logo_dark_path, true) }}"
                    @if (isset($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
            @endif
        </figure>
    </header>
    <div class="flex min-h-screen flex-col items-center justify-center py-12">
        <div class="mx-auto w-full pt-20 text-center lg:w-5/12">
            <h1 class="text-[40vw] leading-none opacity-10 sm:text-[212px]">
                @yield('error_code', '404')
            </h1>

            <h3 class="text-4xl font-bold">
                @yield('error_title', __('Looks like you’re lost.'))
            </h3>

            <p class="mb-14 text-sm font-medium opacity-90">
                @yield('error_subtitle', __('We can’t seem to find the page you’re looking for.'))
            </p>

            <div class="mx-auto sm:w-8/12">
                <x-button
                    class="w-full"
                    size="lg"
                    href="{{ route('index') }}"
                >
                    {{ __('Take me back to the homepage') }}
                </x-button>
            </div>
        </div>
    </div>
@endsection
