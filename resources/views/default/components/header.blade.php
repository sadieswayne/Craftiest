<header class="lqd-header relative flex h-[--header-height] border-b border-header-border bg-header-background text-xs font-medium transition-colors max-lg:h-[65px]">
    <div @class([
        'lqd-header-container flex w-full grow gap-2 px-4 max-lg:w-full max-lg:max-w-none',
        'container' => !$attributes->get('layout-wide'),
        'container-fluid' => $attributes->get('layout-wide'),
        Theme::getSetting('wideLayoutPaddingX', '') =>
            filled(Theme::getSetting('wideLayoutPaddingX', '')) &&
            $attributes->get('layout-wide'),
    ])>

        {{-- Mobile nav toggle and logo --}}
        <div class="mobile-nav-logo flex items-center gap-3 lg:hidden">
            <button
                class="lqd-mobile-nav-toggle size-10 flex items-center justify-center"
                type="button"
                x-init
                @click.prevent="$store.mobileNav.toggleNav()"
                :class="{ 'lqd-is-active': !$store.mobileNav.navCollapse }"
            >
                <span class="lqd-mobile-nav-toggle-icon relative h-[2px] w-5 rounded-xl bg-current"></span>
            </button>
            <x-header-logo />
        </div>

        {{-- Title slot --}}
        @if ($title ?? false)
            <div class="header-title-container peer/title hidden items-center lg:flex">
                <h1 class="m-0 font-semibold">
                    {{ $title }}
                </h1>
            </div>
        @endif

        @includeFirst(['focus-mode::header', 'components.includes.ai-tools', 'vendor.empty'])

        {{-- Search form --}}
        <div class="header-search-container flex items-center peer-[&.header-title-container]/title:grow peer-[&.header-title-container]/title:justify-center">
            <x-header-search />
        </div>

        <div class="header-actions-container flex grow justify-end gap-4 max-lg:basis-2/3 max-lg:gap-2">

            {{-- Action buttons --}}
            @if ($actions ?? false)
                {{ $actions }}
            @else
                <div class="flex items-center max-xl:gap-2 max-lg:hidden xl:gap-3">
                    @if (Auth::user()->isAdmin())
                        <x-button
                            href="{{ route('dashboard.admin.index') }}"
                            variant="ghost-shadow"
                        >
                            {{ __('Admin Panel') }}
                        </x-button>
                    @endif

                    @if ($settings_two->liquid_license_type == 'Extended License')
                        @if ($subscription = getSubscription())
                            <x-button
                                class="max-xl:hidden"
                                href="{{ route('dashboard.user.payment.subscription') }}"
                                variant="ghost-shadow"
                            >
                                {{ $subscription?->plan?->name }} -  {{ getSubscriptionDaysLeft() }}
                                {{ __('Days Left') }}
                            </x-button>
                        @else
                            <x-button
                                class="max-xl:hidden"
                                href="{{ route('dashboard.user.payment.subscription') }}"
                                variant="ghost-shadow"
                            >
                                {{ __('No Active Subscription') }}
                            </x-button>
                        @endif

                        <x-button
                            class="max-xl:hidden"
                            href="{{ route('dashboard.user.payment.subscription') }}"
                        >
                            <x-tabler-bolt class="size-4 fill-current" />
                            <span class="max-lg:hidden">
                                {{ __('Upgrade') }}
                            </span>
                        </x-button>
                    @endif
                </div>
            @endif

            <div class="flex items-center gap-4 max-lg:gap-2">
                {{-- Dark/light switch --}}
                @if (Theme::getSetting('dashboard.supportedColorSchemes') === 'all')
                    <x-light-dark-switch />
                @endif

                @includeFirst(['focus-mode::ai-tools-button', 'components.includes.ai-tools-button', 'vendor.empty'])

                @if (setting('notification_active', 0))
                    {{-- Notifications --}}
                    <x-notifications />
                @endif

                {{-- Language dropdown --}}
                @if (count(explode(',', $settings_two->languages)) > 1)
                    <x-language-dropdown />
                @endif

                {{-- Upgrade button on mobile --}}
                <x-button
                    class="lqd-header-upgrade-btn size-10 flex items-center justify-center border p-0 text-current dark:bg-white/[3%] lg:hidden"
                    variant="link"
                    href="{{ route('dashboard.user.payment.subscription') }}"
                >
                    <x-tabler-bolt stroke-width="1.5" />
                </x-button>

                {{-- User menu --}}
                <x-user-dropdown />
            </div>
        </div>
    </div>
</header>
