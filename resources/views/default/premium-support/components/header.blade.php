@php
    $nav_links = [
        'Home' => '#premium-support',
        'All Access' => '#premium-support-access',
        'Premium Support' => '#premium-support-support',
        'Free Customization' => '#premium-support-customization',
    ];
@endphp

<header
    class="absolute inset-x-0 top-0 z-10"
    x-data="{ mobileMenuVisible: false }"
>
    <div
        class="grid grid-flow-col items-center px-10 py-8 max-xl:grid-cols-2 max-xl:py-5 max-sm:px-5 xl:[grid-template-columns:20%_minmax(580px,100%)_20%]">
        <a href="#">
            @if (isset($setting->logo_dashboard))
                <img
                    class="h-auto group-[.navbar-shrinked]/body:hidden dark:hidden"
                    src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                    @if (isset($setting->logo_dashboard_2x_path) && !empty($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x"
                    @endif
                    alt="{{ $setting->site_name }}"
                >
                <img
                    class="hidden h-auto group-[.navbar-shrinked]/body:hidden dark:block"
                    src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}"
                    @if (isset($setting->logo_dashboard_dark_2x_path) && !empty($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x"
                    @endif
                    alt="{{ $setting->site_name }}"
                >
            @else
                <img
                    class="h-auto group-[.navbar-shrinked]/body:hidden dark:hidden"
                    src="{{ custom_theme_url($setting->logo_path, true) }}"
                    @if (isset($setting->logo_2x_path) && !empty($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
                <img
                    class="hidden h-auto group-[.navbar-shrinked]/body:hidden dark:block"
                    src="{{ custom_theme_url($setting->logo_dark_path, true) }}"
                    @if (isset($setting->logo_dark_2x_path) && !empty($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x"
                    @endif
                    alt="{{ $setting->site_name }}"
                >
            @endif
        </a>

        <nav
            class="flex w-full justify-center whitespace-nowrap transition-all max-xl:invisible max-xl:absolute max-xl:inset-x-10 max-xl:top-full max-xl:w-auto max-xl:origin-top max-xl:scale-95 max-xl:opacity-0 max-sm:inset-x-5 max-xl:[&.lqd-is-active]:visible max-xl:[&.lqd-is-active]:scale-100 max-xl:[&.lqd-is-active]:opacity-100"
            :class="{ 'lqd-is-active': mobileMenuVisible }"
        >
            <ul
                class="flex gap-2 rounded-full border border-white/15 bg-black/15 px-4 py-3 leading-tight text-white/90 backdrop-blur-md max-xl:w-full max-xl:flex-col max-xl:items-center max-xl:gap-y-5 max-xl:rounded-xl max-xl:text-center">
                @foreach ($nav_links as $label => $href)
                    <li>
                        <a
                            class="px-6 py-1"
                            href="{{ $href }}"
                        >
                            @lang($label)
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex justify-end">
            <a
                class="flex items-center gap-2 rounded-full border border-white/15 bg-black/15 px-7 py-3 leading-tight text-white/90 backdrop-blur-md transition-all hover:scale-110 hover:bg-white hover:text-black max-xl:hidden"
                href="{{ app(\App\Domains\Marketplace\Repositories\ExtensionRepository::class)->subscriptionPayment() }}"
                target="_blank"
            >
                @lang('Subscribe Now')
            </a>
            <button
                class="size-12 group inline-flex items-center justify-center rounded-full border border-white/15 bg-black/15 text-white xl:hidden"
                @click.prvent="mobileMenuVisible = !mobileMenuVisible"
                :class="{ 'lqd-is-active': mobileMenuVisible }"
            >
                <x-tabler-x class="size-5 hidden group-[&.lqd-is-active]:block"/>
                <x-tabler-menu-deep class="size-5 group-[&.lqd-is-active]:hidden"/>
            </button>
        </div>
    </div>
</header>
