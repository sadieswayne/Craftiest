<a
    class="lqd-skip-link pointer-events-none fixed start-7 top-7 z-[90] rounded-md bg-background px-3 py-1 text-lg opacity-0 shadow-xl focus-visible:pointer-events-auto focus-visible:opacity-100 focus-visible:outline-primary"
    href="#lqd-titlebar"
>
    {{ __('Skip to content') }}
</a>

<button
    class="lqd-navbar-expander fixed start-[--navbar-width] top-[calc(var(--header-height)/2)] z-[999] inline-flex size-6 -translate-x-1/2 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full border-0 bg-foreground/10 p-0 text-heading-foreground backdrop-blur-sm transition-all hover:bg-heading-foreground hover:text-heading-background group-[.navbar-shrinked]/body:!start-[80px] group-[.navbar-shrinked]/body:rotate-180 max-lg:hidden"
    x-init
    @click.prevent="$store.navbarShrink.toggle()"
>
    <x-tabler-chevron-left class="w-4" />
</button>

<aside
    class="lqd-navbar max-lg:rounded-b-5 z-[99] w-[--navbar-width] shrink-0 overflow-hidden rounded-ee-navbar-ee rounded-es-navbar-es rounded-se-navbar-se rounded-ss-navbar-ss border-e border-navbar-border bg-navbar-background text-navbar font-medium text-navbar-foreground transition-all max-lg:invisible max-lg:absolute max-lg:left-0 max-lg:top-[65px] max-lg:z-[99] max-lg:max-h-[calc(85vh-2rem)] max-lg:min-h-0 max-lg:w-full max-lg:origin-top max-lg:-translate-y-2 max-lg:scale-95 max-lg:overflow-y-auto max-lg:bg-background max-lg:p-0 max-lg:opacity-0 max-lg:shadow-xl lg:sticky lg:top-0 lg:h-screen max-lg:[&.lqd-is-active]:visible max-lg:[&.lqd-is-active]:translate-y-0 max-lg:[&.lqd-is-active]:scale-100 max-lg:[&.lqd-is-active]:opacity-100"
    data-name="{{ \App\Enums\Introduction::SIDEBAR }}"
    x-init
    :class="{ 'lqd-is-active': !$store.mobileNav.navCollapse }"
>
    <div class="lqd-navbar-inner -me-navbar-me h-full overflow-y-auto overscroll-contain pe-navbar-pe ps-navbar-ps">
        <div
            class="lqd-navbar-logo relative flex min-h-[--header-height] max-w-full items-center pe-navbar-link-pe ps-navbar-link-ps group-[.navbar-shrinked]/body:w-full group-[.navbar-shrinked]/body:justify-center group-[.navbar-shrinked]/body:px-0 group-[.navbar-shrinked]/body:text-center max-lg:hidden">
            <a
                class="block px-0"
                href="{{ route('dashboard.index') }}"
            >
                @if (isset($setting->logo_dashboard))
                    <img
                        class="h-auto w-full group-[.navbar-shrinked]/body:hidden dark:hidden"
                        src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                        @if (isset($setting->logo_dashboard_2x_path) && !empty($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                    <img
                        class="hidden h-auto w-full group-[.navbar-shrinked]/body:hidden dark:block"
                        src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}"
                        @if (isset($setting->logo_dashboard_dark_2x_path) && !empty($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                @else
                    <img
                        class="h-auto w-full group-[.navbar-shrinked]/body:hidden dark:hidden"
                        src="{{ custom_theme_url($setting->logo_path, true) }}"
                        @if (isset($setting->logo_2x_path) && !empty($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                    <img
                        class="hidden h-auto w-full group-[.navbar-shrinked]/body:hidden dark:block"
                        src="{{ custom_theme_url($setting->logo_dark_path, true) }}"
                        @if (isset($setting->logo_dark_2x_path) && !empty($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                @endif

                <!-- collapsed -->
                <img
                    class="mx-auto hidden h-auto w-full max-w-10 group-[.navbar-shrinked]/body:block dark:!hidden"
                    src="{{ custom_theme_url($setting->logo_collapsed_path, true) }}"
                    @if (isset($setting->logo_collapsed_2x_path) && !empty($setting->logo_collapsed_2x_path)) srcset="/{{ $setting->logo_collapsed_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
                <img
                    class="mx-auto hidden h-auto w-full max-w-10 group-[.theme-dark.navbar-shrinked]/body:block"
                    src="{{ custom_theme_url($setting->logo_collapsed_dark_path, true) }}"
                    @if (isset($setting->logo_collapsed_dark_2x_path) && !empty($setting->logo_collapsed_dark_2x_path)) srcset="/{{ $setting->logo_collapsed_dark_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >

            </a>
        </div>

        <nav
            class="lqd-navbar-nav"
            id="navbar-menu"
        >
            <ul class="lqd-navbar-ul">
                @include('panel.layout.partials.menu')
                <!-- Menu cache -->
{{--                                {!!--}}
{{--                                 \App\Caches\BladeCache::navMenu(fn() => view('panel.layout.partials.menu')->render())--}}
{{--                                !!}--}}
                @if (Auth::user()->isAdmin())
                    @if ($app_is_not_demo && setting('premium_support', true))
                        <x-navbar.item>
                            <x-navbar.link
                                label="{{ __('Premium Membership') }}"
                                href="#"
                                icon="tabler-diamond"
                                trigger-type="modal"
                            >
                                <x-slot:modal>
                                    @includeIf('premium-support.index')
                                </x-slot:modal>
                            </x-navbar.link>
                        </x-navbar.item>
                    @endif
                @endif

				@if($app_is_demo)
                    {!! \Illuminate\Support\Facades\Cache::remember('components.navbar.partials.credit-for-menu', 3600 * 36000, function () {
                        return view('components.navbar.partials.credit-for-menu')->render();
                    }) !!}
                @else
                    @include('components.navbar.partials.credit-for-menu')
                @endif

                @if ($setting->feature_affilates && (\auth()->user()?->affiliate_status === 1))
                    <x-navbar.item class="group-[&.navbar-shrinked]/body:hidden">
                        <x-navbar.divider />
                    </x-navbar.item>

                    <x-navbar.item class="group-[&.navbar-shrinked]/body:hidden">
                        <x-navbar.label>
                            {{ __('Affiliation') }}
                        </x-navbar.label>
                    </x-navbar.item>

                    <x-navbar.item class="pb-navbar-link-pb pe-navbar-link-pe ps-navbar-link-ps pt-navbar-link-pt group-[&.navbar-shrinked]/body:hidden">
                        <div
                            class="lqd-navbar-affiliation inline-block w-full rounded-xl border border-navbar-divider px-8 py-4 text-center text-2xs leading-tight transition-border">
                            <p class="m-0 mb-2 text-[20px] not-italic">🎁</p>
                            <p class="mb-4">{{ __('Invite your friend and get') }}
                                {{ $setting->affiliate_commission_percentage }}%
                                @if ($is_onetime_commission)
                                    {{ __('on their first purchase.') }}
                                @else
                                    {{ __('on all their purchases.') }}
                                @endif
                            </p>
                            <x-button
                                class="text-3xs"
                                href="{{ route('dashboard.user.affiliates.index') }}"
                                variant="ghost-shadow"
                            >
                                {{ __('Invite') }}
                            </x-button>
                        </div>
                    </x-navbar.item>
                @endif
            </ul>
        </nav>
    </div>
</aside>