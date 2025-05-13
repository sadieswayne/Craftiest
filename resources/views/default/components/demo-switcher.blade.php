<div
    class="lqd-demo-switcher fixed start-0 top-0 z-[99999]"
    x-data="{ open: false }"
    @keydown.window.escape="open = false"
>
    <button
        @class([
            'size-[60px] fixed end-0 top-40 inline-grid origin-right rotate-0 place-content-center rounded-s-xl bg-background text-center text-heading-foreground shadow-2xl backdrop-blur-lg transition-all [--rotate-y:0deg] [transform-style:preserve-3d] [transform:perspective(600px)_rotateY(var(--rotate-y))] active:[--rotate-y:-15deg]',
            'dark:bg-white/10' => $themesType === 'Dashboard',
        ])
        type="button"
        @click.prevent="open = !open"
    >
        <x-tabler-brush class="size-6" />
        <span class="sr-only">
            {{ __('Toggle Demo Switcher') }}
        </span>
    </button>

    <div
        class="pointer-events-none invisible fixed inset-0 z-[9999] flex justify-center p-4 opacity-0 transition-all lg:px-4 lg:py-20"
        :class="{ 'opacity-0': !open, 'invisible': !open, 'pointer-events-none': !open }"
    >
        <div
            class="absolute inset-0 scale-95 bg-heading-foreground/50 opacity-0 backdrop-blur-lg transition-all"
            :class="{ 'opacity-0': !open, 'scale-95': !open }"
            @click="open = false"
        ></div>
        <div
            class="m-auto h-[calc(100vh-50px)] max-w-[1330px] grow scale-95 overflow-y-auto rounded-3xl bg-background/90 p-5 opacity-0 backdrop-blur-lg transition-all delay-150 lg:h-[calc(100vh-200px)] lg:px-10 lg:py-8"
            :class="{ 'opacity-0': !open, 'scale-95': !open }"
        >
            <div class="flex items-center justify-between gap-4">
                <div>
                    @if (isset($setting->logo_dashboard))
                        <img
                            @class([
                                'h-auto w-full',
                                'dark:hidden' => $themesType === 'Dashboard',
                            ])
                            src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                            @if (isset($setting->logo_dashboard_2x_path) && !empty($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                            alt="{{ $setting->site_name }}"
                        >
                        @if ($themesType === 'Dashboard')
                            <img
                                class="hidden h-auto w-full dark:block"
                                src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}"
                                @if (isset($setting->logo_dashboard_dark_2x_path) && !empty($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                                alt="{{ $setting->site_name }}"
                            >
                        @endif
                    @else
                        <img
                            @class([
                                'h-auto w-full',
                                'dark:hidden' => $themesType === 'Dashboard',
                            ])
                            src="{{ custom_theme_url($setting->logo_path, true) }}"
                            @if (isset($setting->logo_2x_path) && !empty($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                            alt="{{ $setting->site_name }}"
                        >
                        @if ($themesType === 'Dashboard')
                            <img
                                class="hidden h-auto w-full dark:block"
                                src="{{ custom_theme_url($setting->logo_dark_path, true) }}"
                                @if (isset($setting->logo_dark_2x_path) && !empty($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                                alt="{{ $setting->site_name }}"
                            >
                        @endif
                    @endif
                </div>
                <div>
                    <button
                        class="size-9 inline-grid place-content-center text-heading-foreground transition-all hover:scale-110"
                        type="button"
                        @click="open = false"
                    >
                        <x-tabler-x class="size-6" />
                    </button>
                </div>
            </div>

            <div class="container py-7">
                <div class="mb-9 lg:w-1/2">
                    <h3 class="mb-1 text-[33px] leading-tight">
                        @lang('Customize Appearance')
                    </h3>
                    <p class="text-sm">
                        @lang('Customize the visual appearence of MagicAI with a single click and complement the design principles of your brand identity. ')
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 lg:gap-12">
                    @foreach ($themes as $theme)
                        @continue($theme['theme_type'] !== $themesType && $theme['theme_type'] !== 'All')

                        <div class="group relative">
                            <figure class="relative overflow-hidden rounded-xl border-[20px] border-background shadow-[0_4px_15px_rgba(0,0,0,0.05)]">
                                <img
                                    class="aspect-video w-full object-cover object-top"
                                    src="{{ $theme['icon'] }}"
                                    alt="{{ $theme['name'] }}"
                                    width="490"
                                    height="320"
                                    loading="lazy"
                                >
                                <span
                                    class="absolute inset-0 flex scale-110 items-center justify-center gap-2 bg-foreground/50 text-background opacity-0 backdrop-blur-md transition-all group-hover:scale-100 group-hover:opacity-100"
                                >
                                    <x-tabler-eye />
                                    <span>
                                        @lang('Live Preview')
                                    </span>
                                </span>
                            </figure>

                            <div class="px-3 pt-4 text-center">
                                <h3 class="mb-2 text-xl font-bold">
                                    @lang($theme['name'])
                                </h3>
                                <p class="mb-3 flex items-center justify-center gap-1.5 text-xs font-medium">
                                    @if ($theme['price'] > 0)
                                        <span @class([
                                            'size-2 inline-block rounded-full',
                                            'bg-green-600' => false, // Free themes
                                            'bg-purple-700' => true, // Premium themes
                                        ])></span>
                                        @lang('Premium Theme')
                                    @else
                                        <span @class([
                                            'size-2 inline-block rounded-full',
                                            'bg-green-600' => true, // Free themes
                                            'bg-purple-700' => false, // Premium themes
                                        ])></span>
                                        @lang('Free Theme')
                                    @endif
                                </p>
                            </div>

                            <a
                                class="absolute inset-0 opacity-0"
                                href="https://{{ $theme['slug'] == 'default' ? 'magicai.liquid-themes.com' : $theme['slug'] . '.projecthub.ai' }}"
                                target="_blank"
                            ></a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
