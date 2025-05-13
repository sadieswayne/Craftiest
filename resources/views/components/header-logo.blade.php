<a
        {{ $attributes->class(['flex shrink-0 items-center justify-center']) }}
        href="{{  (route('dashboard.index')) }}"
>
    @if (isset($setting->logo_dashboard))
        <img
                class="dark:hidden"
                src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                @if (isset($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                alt="{{ $setting->site_name }}"
        >
        <img
                class="hidden dark:block"
                src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}"
                @if (isset($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x"
                @endif
                alt="{{ $setting->site_name }}"
        >
    @else
        <img
                class="dark:hidden"
                src="{{ custom_theme_url($setting->logo_path, true) }}"
                @if (isset($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                alt="{{ $setting->site_name }}"
        >
        <img
                class="hidden dark:block"
                src="{{ custom_theme_url($setting->logo_dark_path, true) }}"
                @if (isset($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                alt="{{ $setting->site_name }}"
        >
    @endif
</a>
