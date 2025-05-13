@php
    $theme = get_theme();
    $disable_floating_menu = true;
    $wide_layout_px_class = Theme::getSetting('wideLayoutPaddingX', '');
    $theme_google_fonts = Theme::getSetting('dashboard.googleFonts');
    $sidebarEnabledPages = Theme::getSetting('dashboard.sidebarEnabledPages') ?? [];
    $has_sidebar = in_array(Route::currentRouteName(), $sidebarEnabledPages, true) || (isset($has_sidebar) && $has_sidebar);
    $body_class = Theme::getSetting('dashboard.bodyClass', '');

    if (!empty($wide_layout_px)) {
        $wide_layout_px_class = $wide_layout_px;
    }
@endphp
<!doctype html>
<html
    class="scroll-smooth"
    lang="{{ LaravelLocalization::getCurrentLocale() }}"
    dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}"
>
@include('panel.layout.partials.head')

<body
    data-theme="{{ setting('dash_theme') }}"
    @class([
        @twMerge(
            'group/body bg-background font-body text-xs text-foreground antialiased transition-bg',
            $body_class),
        'has-sidebar' => $has_sidebar,
        'is-admin-page' =>
            Auth::check() &&
            (Route::is('dashboard.admin*') ||
                Route::is('dashboard.blog*') ||
                Route::is('dashboard.page*')),
        'is-auth-page' => Route::is('login', 'register', 'forgot_password'),
        'hide-navbar' => isset($disable_navbar),
        'hide-footer' => isset($disable_footer),
        'hide-header' => isset($disable_header),
        'hide-titlebar' => isset($disable_titlebar),
    ])
>

	@if($app_is_not_demo)
		@includeFirst(['onboarding-pro::banner', 'vendor.empty'])
	@endif

    @includeIf('panel.layout.after-body-open')

    @include('panel.layout.partials.mode-script')

    @include('panel.layout.partials.loading')

    <div class="lqd-page relative flex min-h-full flex-col">

        <div class="lqd-page-wrapper grow-1 flex">
            @auth
                @if (!isset($disable_navbar))
					@include('panel.layout.navbar')
				@endif
            @endauth
            <div class="lqd-page-content-wrap flex grow flex-col overflow-hidden">
                @if ($good_for_now)
                    @auth
                        @if (!isset($disable_header))
                            @include('panel.layout.header', ['layout_wide', isset($layout_wide) ? $layout_wide : ''])
                        @endif
                        @if (!isset($disable_titlebar))
                            @include('panel.layout.titlebar', ['layout_wide', isset($layout_wide) ? $layout_wide : ''])
                        @endif
                    @endauth
                    @yield('before_content_container')
                    <div @class([
                        'lqd-page-content-container',
                        'h-full',
                        'container' => !isset($layout_wide) || empty($layout_wide),
                        'container-fluid' => isset($layout_wide) && !empty($layout_wide),
                        $wide_layout_px_class =>
                            filled($wide_layout_px_class) &&
                            (isset($layout_wide) && !empty($layout_wide)),
                    ])>

                        @yield('content')

						@if($app_is_not_demo)
                        	@includeFirst(['onboarding-pro::survey', 'vendor.empty'])
						@endif
                    </div>
                @elseif(Auth::check() && !$good_for_now && Route::currentRouteName() != 'dashboard.admin.settings.general')
                    <div @class([
                        'lqd-page-content-container',
                        'container' => !isset($layout_wide) || empty($layout_wide),
                        'container-fluid' => isset($layout_wide) && !empty($layout_wide),
                        $wide_layout_px_class =>
                            filled($wide_layout_px_class) &&
                            (isset($layout_wide) && !empty($layout_wide)),
                    ])>
                        @include('vendor.installer.magicai_c4st_Act')
                    </div>
                @else
                    @auth
                        @if (!isset($disable_header))
                            @include('panel.layout.header', ['layout_wide', isset($layout_wide) ? $layout_wide : ''])
                        @endif
                        @if (!isset($disable_titlebar))
                            @include('panel.layout.titlebar', ['layout_wide', isset($layout_wide) ? $layout_wide : ''])
                        @endif
                    @endauth
                    @yield('before_content_container')
                    <div @class([
                        'lqd-page-content-container',
                        'container' => !isset($layout_wide) || empty($layout_wide),
                        'container-fluid' => isset($layout_wide) && !empty($layout_wide),
                        $wide_layout_px_class =>
                            filled($wide_layout_px_class) &&
                            (isset($layout_wide) && !empty($layout_wide)),
                    ])>

                        @yield('content')
                    </div>
                @endif

                @auth
                    @if (!isset($disable_footer))
                        @include('panel.layout.footer')
                    @endif

                    @if ($has_sidebar && (!isset($disable_default_sidebar) || empty($disable_default_sidebar)))
                        @includeIf('panel.layout.sidebar')
                    @endif
                @endauth
            </div>
        </div>
    </div>

    @auth
        @if (!isset($disable_floating_menu))
            <x-floating-menu />
        @endif
        @if (!isset($disable_mobile_bottom_menu))
            <x-bottom-menu />
        @endif
    @endauth

    @if (!isset($disableChatbot))
        @includeWhen(in_array($settings_two->chatbot_status, ['dashboard', 'both']) &&
                !activeRoute('dashboard.user.openai.chat.chat', 'dashboard.user.openai.webchat.workbook', 'dashboard.user.advanced-image.index') &&
                !(route('dashboard.user.openai.generator.workbook', 'ai_vision') == url()->current()) &&
                !(route('dashboard.user.openai.generator.workbook', 'ai_chat_image') == url()->current()) &&
                !(route('dashboard.user.openai.generator.workbook', 'ai_pdf') == url()->current()),
            'panel.chatbot.widget')
    @endif

    <script src="{{ custom_theme_url('/assets/libs/underscore/underscore-umd-min.js') }}"></script>
    @include('panel.layout.scripts')

    @if (session()->has('message'))
        <script>
            toastr.{{ session('type') }}('{{ session('message') }}');
        </script>
    @endif

    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error('{{ $error }}');
            @endforeach
        </script>
    @endif

    @auth
        {{--        <script type="module"> --}}
        {{--            Echo.private(`App.Models.User.{{ auth()->user()?->id }}`) --}}
        {{--                .listen(".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated", (notification) => { --}}
        {{--                    if (Alpine) { --}}
        {{--                        Alpine.store('notifications').add({ --}}
        {{--                            id: notification.id, --}}
        {{--                            title: notification.data.title, --}}
        {{--                            message: notification.data.message, --}}
        {{--                            link: notification.data.link, --}}
        {{--                            unread: true --}}
        {{--                        }) --}}
        {{--                    } --}}
        {{--                }); --}}
        {{--        </script> --}}
    @endauth

    @stack('script')

    <script src="{{ custom_theme_url('/assets/js/frontend.js') }}"></script>

    @if ($setting->dashboard_code_before_body != null)
        {!! $setting->dashboard_code_before_body !!}
    @endif

    <script src="{{ custom_theme_url('assets/js/chatbot.js') }}"></script>

    @includeIf('panel.layout.before-body-close')

	@if($app_is_not_demo)
		@auth()
			@if (auth()->user()->isAdmin())
				<script src="{{ custom_theme_url('/assets/js/panel/update-check.js') }}"></script>
			@endif
		@endauth
		<script src="{{ custom_theme_url('/assets/libs/introjs/intro.min.js') }}"></script>
		@includeIf('seo-tool::particles.generate-seo-script')
		@include('panel.layout.includes.lazy-intercom')
		@include('panel.layout.includes.subscription-status')
	@endif

	@if ($app_is_demo)
		<x-demo-switcher themes-type="Dashboard" />
	@endif

    @livewireScriptConfig()

    <template id="typing-template">
        <div class="lqd-typing relative inline-flex items-center gap-3 rounded-full bg-secondary !px-3 !py-2 text-xs font-medium leading-none text-secondary-foreground">
            {{ __('Typing') }}
            <div class="lqd-typing-dots flex h-5 items-center gap-1">
                <span class="lqd-typing-dot inline-block !h-1 !w-1 rounded-full !bg-current opacity-40 ![animation-delay:0.2s]"></span>
                <span class="lqd-typing-dot inline-block !h-1 !w-1 rounded-full !bg-current opacity-60 ![animation-delay:0.3s]"></span>
                <span class="lqd-typing-dot inline-block !h-1 !w-1 rounded-full !bg-current opacity-80 ![animation-delay:0.4s]"></span>
            </div>
        </div>
    </template>

    <template id="copy-btns-template">
        <div
            class="pointer-events-none invisible absolute bottom-full flex translate-y-1 flex-col gap-2 pb-2 opacity-0 transition-all group-[&.active]/copy-wrap:pointer-events-auto group-[&.active]/copy-wrap:visible group-[&.active]/copy-wrap:translate-y-0 group-[&.active]/copy-wrap:opacity-100">
            <button
                class="group/btn relative inline-flex size-9 items-center justify-center rounded-full bg-white p-0 text-[12px] text-black shadow-lg transition-all hover:scale-110"
                data-copy-type="md"
                type="button"
            >
                <x-tabler-markdown
                    class="size-5"
                    stroke-width="1.5"
                />
                <span
                    class="absolute end-full top-1/2 me-1 -translate-y-1/2 translate-x-1 whitespace-nowrap rounded-full bg-white px-3 py-1 font-medium opacity-0 shadow-lg transition-all group-hover/btn:translate-x-0 group-hover/btn:opacity-100"
                >
                    @lang('Copy Markdown')
                </span>
            </button>
            <button
                class="group/btn relative inline-flex size-9 items-center justify-center rounded-full bg-white p-0 text-[12px] text-black shadow-lg transition-all hover:scale-110"
                data-copy-type="html"
                type="button"
            >
                <x-tabler-file-type-html
                    class="size-5"
                    stroke-width="1.5"
                />
                <span
                    class="absolute end-full top-1/2 me-1 -translate-y-1/2 translate-x-1 whitespace-nowrap rounded-full bg-white px-3 py-1 font-medium opacity-0 shadow-lg transition-all group-hover/btn:translate-x-0 group-hover/btn:opacity-100"
                >
                    @lang('Copy HTML')
                </span>
            </button>
        </div>
    </template>
</body>

</html>
