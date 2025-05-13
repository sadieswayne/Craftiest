@php
    $plan = Auth::user()->activePlan();
    $plan_type = 'regular';
    // $team = Auth::user()->getAttribute('team');
    $teamManager = Auth::user()->getAttribute('teamManager');

    if ($plan != null) {
        $plan_type = strtolower($plan->plan_type);
    }

    $titlebar_links = [
        [
            'label' => 'All',
            'link' => '#all',
        ],
        [
            'label' => 'AI Assistant',
            'link' => '#all',
        ],
        [
            'label' => 'Your Plan',
            'link' => '#plan',
        ],
        [
            'label' => 'Team Members',
            'link' => '#team',
        ],
        [
            'label' => 'Recent',
            'link' => '#recent',
        ],
        [
            'label' => 'Documents',
            'link' => '#documents',
        ],
        [
            'label' => 'Templates',
            'link' => '#templates',
        ],
        [
            'label' => 'Overview',
            'link' => '#all',
        ],
    ];

    $premium_features = \App\Models\OpenAIGenerator::query()->where('active', 1)->where('premium', 1)->get()->pluck('title')->toArray();
    $user_is_premium = false;
    $plan = auth()->user()?->relationPlan;
    if ($plan) {
        $planType = strtolower($plan->plan_type ?? 'all');
        if ($plan->plan_type === 'all' || $plan->plan_type === 'premium') {
            $user_is_premium = true;
        }
    }

    $style_string = '';

    if (setting('announcement_background_color')) {
        $style_string .= '.lqd-card.lqd-announcement-card { background-color: ' . setting('announcement_background_color') . ';}';
    }

    if (setting('announcement_background_image')) {
        $style_string .= '.lqd-card.lqd-announcement-card { background-image: url(' . setting('announcement_background_image') . '); }';
    }

    if (setting('announcement_background_color_dark')) {
        $style_string .= '.theme-dark .lqd-card.lqd-announcement-card { background-color: ' . setting('announcement_background_color_dark') . ';}';
    }

    if (setting('announcement_background_image_dark')) {
        $style_string .= '.theme-dark .lqd-card.lqd-announcement-card { background-image: url(' . setting('announcement_background_image_dark') . '); }';
    }

    $favoriteOpenAis = cache('favorite_openai');
@endphp

@if (filled($style_string))
    @push('css')
        <style>
            {{ $style_string }}
        </style>
    @endpush
@endif

@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Dashboard'))
@section('titlebar_title')
    {{ __('Welcome') }}, {{ auth()->user()?->name }}.
@endsection
@section('titlebar_after')
    <ul
        class="lqd-filter-list mt-1 flex list-none flex-wrap items-center gap-x-4 gap-y-2 text-heading-foreground max-sm:gap-3"
        x-data="{}"
    >
        @foreach ($titlebar_links as $link)
            <li>
                <x-button
                    @class([
                        'lqd-filter-btn inline-flex rounded-full px-2.5 py-0.5 text-2xs leading-tight transition-colors hover:translate-y-0 hover:bg-foreground/5 [&.active]:bg-foreground/5',
                        'active' => $loop->first,
                    ])
                    variant="ghost"
                    href="{{ $link['link'] }}"
                    x-data="{}"
                >
                    @lang($link['label'])
                </x-button>
            </li>
        @endforeach
    </ul>
@endsection

@section('content')
    <div class="flex flex-wrap justify-between gap-8 py-5">
        <!-- start: landing badge -->
        <div
            class="grid w-full grid-cols-1 gap-10"
            id="all"
        >
            @if (setting('announcement_active', 0) && !auth()->user()?->dash_notify_seen)
                <div
                    class="lqd-announcement"
                    data-name="{{ \App\Enums\Introduction::DASHBOARD_FIRST }}"
                    x-data="{ show: true }"
                    x-ref="announcement"
                >
                    <script>
                        const announcementDismissed = localStorage.getItem('lqd-announcement-dismissed');
                        if (announcementDismissed) {
                            document.querySelector('.lqd-announcement').style.display = 'none';
                        }
                    </script>

                    <x-card
                        class="lqd-announcement-card relative bg-cover bg-center"
                        size="lg"
                        x-ref="announcementCard"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h3 class="mb-3">
                                    @lang(setting('announcement_title', 'Welcome'))
                                </h3>
                                <p class="mb-4">
                                    @lang(setting('announcement_description', 'We are excited to have you here. Explore the marketplace to find the best AI models for your needs.'))
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <x-button
                                        class="font-medium"
                                        href="{{ setting('announcement_url', '#') }}"
                                    >
                                        <x-tabler-plus class="size-4" />
                                        {{ setting('announcement_button_text', 'Try it Now') }}
                                    </x-button>
                                    <x-button
                                        class="font-medium"
                                        href="javascript:void(0)"
                                        variant="ghost-shadow"
                                        hover-variant="danger"
                                        @click.prevent="{{ $app_is_demo ? 'toastr.info(\'This feature is disabled in Demo version.\')' : ' dismiss()' }}"
                                    >
                                        @lang('Dismiss')
                                    </x-button>
                                </div>
                            </div>
                            @if (setting('announcement_image_dark'))
                                <img
                                    class="announcement-img announcement-img-dark peer hidden w-28 shrink-0 dark:block"
                                    src="{{ setting('announcement_image_dark', '/upload/images/speaker.png') }}"
                                    alt="@lang(setting('announcement_title', 'Welcome to MagicAI!'))"
                                >
                            @endif
                            <img
                                class="announcement-img announcement-img-light w-28 shrink-0 dark:peer-[&.announcement-img-dark]:hidden"
                                src="{{ setting('announcement_image', '/upload/images/speaker.png') }}"
                                alt="@lang(setting('announcement_title', 'Welcome to MagicAI!'))"
                            >
                        </div>
                    </x-card>
                </div>
            @endif
            <x-card
                data-name="{{ \App\Enums\Introduction::DASHBOARD_TWO }}"
                size="lg"
            >
                <h3 class="mb-6 flex items-center gap-3">
                    {{-- blade-formatter-disable --}}
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd"
							  d="M18.7588 7.85618L17.1437 8.18336V8.18568C16.3659 8.34353 15.6517 8.72701 15.0905 9.28825C14.5292 9.8495 14.1458 10.5636 13.9879 11.3415L13.6607 12.9565C13.6262 13.1155 13.5383 13.2578 13.4117 13.3599C13.285 13.462 13.1273 13.5177 12.9646 13.5177C12.8019 13.5177 12.6442 13.462 12.5175 13.3599C12.3909 13.2578 12.303 13.1155 12.2685 12.9565L11.9413 11.3415C11.7837 10.5635 11.4003 9.84922 10.839 9.28793C10.2777 8.72663 9.56345 8.34324 8.78546 8.18568L7.17042 7.8585C7.00937 7.82552 6.86464 7.73795 6.76071 7.61058C6.65678 7.48321 6.60001 7.32386 6.60001 7.15946C6.60001 6.99507 6.65678 6.83572 6.76071 6.70835C6.86464 6.58098 7.00937 6.4934 7.17042 6.46043L8.78546 6.13324C9.56339 5.97554 10.2776 5.5921 10.8389 5.03084C11.4001 4.46957 11.7836 3.75536 11.9413 2.97743L12.2685 1.36239C12.303 1.20344 12.3909 1.06109 12.5175 0.959015C12.6442 0.856935 12.8019 0.80127 12.9646 0.80127C13.1273 0.80127 13.285 0.856935 13.4117 0.959015C13.5383 1.06109 13.6262 1.20344 13.6607 1.36239L13.9879 2.97743C14.1458 3.75529 14.5292 4.46943 15.0905 5.03067C15.6517 5.59192 16.3659 5.9754 17.1437 6.13324L18.7588 6.45811C18.9198 6.49108 19.0645 6.57866 19.1685 6.70603C19.2724 6.8334 19.3292 6.99275 19.3292 7.15714C19.3292 7.32154 19.2724 7.48089 19.1685 7.60826C19.0645 7.73563 18.9198 7.8232 18.7588 7.85618ZM6.94895 16.0393L6.51038 16.1286C5.96946 16.2383 5.47282 16.5037 5.08244 16.8939C4.69206 17.2841 4.42523 17.7806 4.31524 18.3214L4.2259 18.76C4.202 18.8835 4.13584 18.9949 4.03877 19.075C3.9417 19.1551 3.81978 19.1989 3.69394 19.1989C3.56809 19.1989 3.44617 19.1551 3.3491 19.075C3.25204 18.9949 3.18587 18.8835 3.16197 18.76L3.07263 18.3214C2.96278 17.7805 2.69599 17.2839 2.30559 16.8937C1.91518 16.5035 1.41847 16.237 0.877485 16.1274L0.43892 16.0381C0.315366 16.0142 0.203985 15.948 0.123895 15.851C0.0438042 15.7539 0 15.632 0 15.5061C0 15.3803 0.0438042 15.2584 0.123895 15.1613C0.203985 15.0642 0.315366 14.9981 0.43892 14.9742L0.877485 14.8848C1.41862 14.7752 1.91545 14.5085 2.30587 14.1181C2.69629 13.7276 2.96299 13.2308 3.07263 12.6897L3.16197 12.2511C3.18587 12.1276 3.25204 12.0162 3.3491 11.9361C3.44617 11.856 3.56809 11.8122 3.69394 11.8122C3.81978 11.8122 3.9417 11.856 4.03877 11.9361C4.13584 12.0162 4.202 12.1276 4.2259 12.2511L4.31524 12.6897C4.42482 13.231 4.69148 13.728 5.08189 14.1186C5.4723 14.5092 5.96915 14.7761 6.51038 14.886L6.94895 14.9753C7.0725 14.9992 7.18388 15.0654 7.26397 15.1625C7.34407 15.2595 7.38787 15.3814 7.38787 15.5073C7.38787 15.6331 7.34407 15.7551 7.26397 15.8521C7.18388 15.9492 7.0725 16.0154 6.94895 16.0393Z"
							  fill="url(#paint0_linear_213_525)"/>
						<defs>
							<linearGradient id="paint0_linear_213_525" x1="1.1976e-07" y1="4.55439" x2="15.5124" y2="18.9291"
											gradientUnits="userSpaceOnUse">
								<stop stop-color="#82E2F4"/>
								<stop offset="0.502" stop-color="#8A8AED"/>
								<stop offset="1" stop-color="#6977DE"/>
							</linearGradient>
						</defs>
					</svg>
					{{-- blade-formatter-enable --}}
                    @lang('Hey, How can I help you?')
                </h3>
                <x-header-search
                    class="mb-5 w-full"
                    class:input="bg-background border-none h-12 text-heading-foreground shadow-[0_4px_8px_rgba(0,0,0,0.05)] placeholder:text-heading-foreground"
                    size="lg"
                    in-content
                />
                <x-button
                    class="group text-[12px] font-medium text-heading-foreground"
                    variant="link"
                    href="{{ $setting->feature_ai_advanced_editor ? LaravelLocalization::localizeUrl(route('dashboard.user.generator.index')) : LaravelLocalization::localizeUrl(route('dashboard.user.openai.list')) }}"
                >
                    @lang('Create a Blank Document')
                    <span
                        class="inline-flex size-9 items-center justify-center rounded-full bg-background shadow transition-all group-hover:scale-110 group-hover:bg-heading-foreground group-hover:text-header-background"
                    >
                        <x-tabler-plus class="size-4" />
                    </span>
                </x-button>
            </x-card>
        </div>
        <!-- end: landing badge -->

        <!-- start: ongoing payment -->
        @if ($ongoingPayments != null)
            <div class="w-full">
                @includeIf('panel.user.finance.ongoingPayments')
            </div>
        @endif
        <!-- end: ongoing payment -->

        <!-- start: finance subscription status -->
        <x-card
            class="{{ showTeamFunctionality() || !$user_is_premium ? 'lg:w-[48%]' : 'lg:w-full' }} w-full text-center"
            class:body="md:px-10 px-5"
            id="plan"
            data-name="{{ \App\Enums\Introduction::DASHBOARD_THREE }}"
            size="lg"
        >
            @includeIf('panel.user.finance.subscriptionStatus')
        </x-card>
        <!-- end: finance subscription status -->

        @if (!$user_is_premium || $app_is_demo)
            <x-card
                class="relative flex w-full flex-col justify-center bg-cover bg-top text-center lg:w-[48%]"
                class:body="flex flex-col only:grow-0 py-8 xl:px-20 static"
            >
                <figure
                    class="pointer-events-none absolute start-0 top-0 z-0 h-full w-full overflow-hidden"
                    aria-hidden="true"
                >
                    <img
                        class="w-full"
                        src="{{ custom_theme_url('/assets/img/bg/premium-card-bg.jpg') }}"
                        alt="{{ __('Premium Features') }}"
                    />
                </figure>
                <div class="relative z-1 flex flex-col">
                    <h4 class="mb-5 text-[17px] text-lg">
                        @lang('Premium Advantages')
                    </h4>
                    <p class="mb-8 text-xs font-medium opacity-60">
                        @lang('Upgrade your plan to unlock new AI capabilities.')
                    </p>
                    <ul class="mb-11 space-y-4 self-center text-xs font-medium text-heading-foreground">
                        {{--						Mr. Hakan wanted me to add this as static for now. --}}
                        @foreach (['Unlimited Credits', 'Access to All Templates', 'External Chatbots', 'o1-mini and DeepSeek R1', 'Premium Support'] as $feature)
                            <li class="flex items-center gap-3.5">
                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 16 16"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M2.09635 7.37072C1.80296 7.37154 1.51579 7.45542 1.26807 7.61264C1.02035 7.76986 0.822208 7.994 0.696564 8.25914C0.570919 8.52427 0.522908 8.81956 0.558084 9.11084C0.59326 9.40212 0.710186 9.67749 0.895335 9.9051L4.84228 14.7401C4.98301 14.9148 5.1634 15.0535 5.36847 15.1445C5.57353 15.2355 5.79736 15.2763 6.02136 15.2635C6.50043 15.2377 6.93295 14.9815 7.20871 14.5601L15.4075 1.35593C15.4089 1.35373 15.4103 1.35154 15.4117 1.34939C15.4886 1.23127 15.4637 0.997192 15.3049 0.850142C15.2613 0.809761 15.2099 0.778736 15.1538 0.75898C15.0977 0.739223 15.0382 0.731153 14.9789 0.735266C14.9196 0.739379 14.8618 0.755589 14.809 0.782896C14.7562 0.810204 14.7095 0.848031 14.6719 0.894048C14.669 0.897666 14.6659 0.90123 14.6628 0.904739L6.39421 10.247C6.36275 10.2826 6.32454 10.3115 6.28179 10.3322C6.23905 10.3528 6.19263 10.3648 6.14522 10.3674C6.09782 10.3699 6.05038 10.363 6.00565 10.3471C5.96093 10.3312 5.91982 10.3065 5.88471 10.2746L3.14051 7.77735C2.8555 7.51608 2.48299 7.37102 2.09635 7.37072Z"
                                        fill="url(#paint0_linear_9208_560_{{ $loop->index }})"
                                    />
                                    <defs>
                                        <linearGradient
                                            id="paint0_linear_9208_560_{{ $loop->index }}"
                                            x1="0.546875"
                                            y1="3.69866"
                                            x2="12.7738"
                                            y2="14.7613"
                                            gradientUnits="userSpaceOnUse"
                                        >
                                            <stop stop-color="#82E2F4" />
                                            <stop
                                                offset="0.502"
                                                stop-color="#8A8AED"
                                            />
                                            <stop
                                                offset="1"
                                                stop-color="#6977DE"
                                            />
                                        </linearGradient>
                                    </defs>
                                </svg>
                                {{ __($feature) }}
                            </li>
                        @endforeach
                    </ul>

                    <x-button
                        class="py-5 text-[18px] font-bold shadow-[0_14px_44px_rgba(0,0,0,0.07)] hover:shadow-2xl hover:shadow-primary/30 dark:hover:bg-primary"
                        href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.payment.subscription')) }}"
                        variant="ghost-shadow"
                    >
                        <span
                            class="bg-gradient-to-r from-[#82E2F4] via-[#8A8AED] to-[#6977DE] bg-clip-text font-bold text-transparent group-hover:from-white group-hover:via-white group-hover:to-white/80"
                        >
                            @lang('Upgrade Your Plan')
                        </span>
                    </x-button>
                </div>
            </x-card>
        @endif
        <!-- end: premiun features -->

        {{-- begin: account summary --}}
        @includeIf('panel.user.dashboard.account-summary')
        {{-- end: account summary --}}

        {{-- begin: invite team --}}
        @if (showTeamFunctionality())
            <x-card
                class="w-full lg:w-[48%]"
                id="team"
                size="lg"
            >
                @if ($team)
                    <figure class="mb-7">
                        <img
                            class="mx-auto w-full lg:w-7/12"
                            src="{{ custom_theme_url('assets/img/team/team.png') }}"
                            alt="Team"
                        >
                    </figure>
                    <p class="mb-6 text-center text-xl font-semibold">
                        @lang('Add your team members’ email address <br> to start collaborating.')
                        📧
                    </p>
                    <form
                        class="flex flex-col gap-3"
                        action="{{ route('dashboard.user.team.invitation.store', $team->id) }}"
                        method="post"
                    >
                        @csrf
                        <input
                            type="hidden"
                            name="team_id"
                            value="{{ $team?->id }}"
                        >
                        <x-forms.input
                            class="mb-6"
                            id="email"
                            size="lg"
                            type="email"
                            name="email"
                            placeholder="{{ __('Email address') }}"
                            required
                        >
                            <x-slot:icon>
                                <x-tabler-mail class="absolute end-3 top-1/2 size-5 -translate-y-1/2" />
                            </x-slot:icon>
                        </x-forms.input>
                        @if ($app_is_demo)
                            <x-button onclick="return toastr.info('This feature is disabled in Demo version.')">
                                @lang('Invite Friends')
                            </x-button>
                        @else
                            <x-button
                                class="py-3"
                                data-name="{{ \App\Enums\Introduction::AFFILIATE_SEND }}"
                                type="submit"
                            >
                                @lang('Invite Friends')
                            </x-button>
                        @endif
                    </form>
                @else
                    <h3 class="mb-6">
                        {{ __('How it Works') }}
                    </h3>

                    <ol class="mb-12 flex flex-col gap-4 text-heading-foreground">
                        <li>
                            <span class="me-2 inline-flex size-7 items-center justify-center rounded-full bg-primary/10 font-extrabold text-primary">
                                1
                            </span>
                            {!! __('You <strong>send your invitation link</strong> to your friends.') !!}
                        </li>
                        <li>
                            <span class="me-2 inline-flex size-7 items-center justify-center rounded-full bg-primary/10 font-extrabold text-primary">
                                2
                            </span>
                            {!! __('<strong>They subscribe</strong> to a paid plan by using your refferral link.') !!}
                        </li>
                        <li>
                            <span class="me-2 inline-flex size-7 items-center justify-center rounded-full bg-primary/10 font-extrabold text-primary">
                                3
                            </span>
                            @if ($is_onetime_commission)
                                {!! __('From their first purchase, you will begin <strong>earning one-time commissions</strong>.') !!}
                            @else
                                {!! __('From their first purchase, you will begin <strong>earning recurring commissions</strong>.') !!}
                            @endif
                        </li>
                    </ol>

                    <form
                        class="flex flex-col gap-3"
                        id="send_invitation_form"
                        onsubmit="return sendInvitationForm();"
                    >
                        <x-forms.input
                            class:label="text-heading-foreground"
                            id="to_mail"
                            label="{{ __('Affiliate Link') }}"
                            size="sm"
                            type="email"
                            name="to_mail"
                            placeholder="{{ __('Email address') }}"
                            required
                        >
                            <x-slot:icon>
                                <x-tabler-mail class="absolute end-3 top-1/2 size-5 -translate-y-1/2" />
                            </x-slot:icon>
                        </x-forms.input>

                        <x-button
                            class="w-full rounded-xl"
                            id="send_invitation_button"
                            type="submit"
                            form="send_invitation_form"
                        >
                            {{ __('Send') }}
                        </x-button>
                    </form>
                @endif
            </x-card>
        @endif
        {{-- end: invite team --}}

        {{-- begin: affiliates --}}
        @includeIf('panel.user.dashboard.affiliates')
        {{-- end: affiliates --}}

        {{-- begin: favorite chatbots --}}
        @includeIf('panel.user.dashboard.favorite-chatbots')
        {{-- end: favorite chatbots --}}

        {{-- begin: add new --}}
        <x-card
            class="flex w-full flex-col"
            id="add-new"
            size="md"
        >
            <x-slot:head
                class="border-0 pb-0 pt-5"
            >
                <h4 class="m-0 text-lg">
                    {{ __('Add New') }}
                </h4>
            </x-slot:head>

            <div class="flex w-full flex-wrap justify-between gap-y-4">
                @if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot') || $app_is_demo)
                    <x-card
                        class="group w-full cursor-pointer transition-all duration-300 hover:bg-gradient-to-r hover:from-[#F0E7FF] hover:to-[#F1F5FF] lg:w-[48%]"
                        class:body="flex flex-col justify-between max-sm:gap-5 gap-16"
                        size="sm"
                    >
                        <svg
                            class="fill-foreground dark:group-hover:fill-background max-sm:mx-auto"
                            width="43"
                            height="38"
                            viewBox="0 0 43 38"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M0.447266 14.305C0.447266 10.9552 3.16283 8.23962 6.51265 8.23962H24.7361C28.0858 8.23962 30.8014 10.9552 30.8014 14.305V27.7078C30.8014 31.0577 28.0858 33.7734 24.7361 33.7734H9.71474C9.49631 33.7734 9.28671 33.8595 9.1314 34.0131L6.86331 36.2561C4.48423 38.6091 0.447266 36.9239 0.447266 33.5777V14.305ZM6.51265 11.1771C4.78517 11.1771 3.38477 12.5775 3.38477 14.305V33.5777C3.38477 34.3146 4.27377 34.6857 4.79768 34.1676L7.06579 31.9245C7.77102 31.2269 8.72287 30.8359 9.71474 30.8359H24.7361C26.4635 30.8359 27.8639 29.4354 27.8639 27.7078V14.305C27.8639 12.5775 26.4635 11.1771 24.7361 11.1771H6.51265Z"
                            />
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M12.1973 6.47164C12.1973 3.12181 14.9128 0.40625 18.2626 0.40625H36.4861C39.8358 0.40625 42.5514 3.12181 42.5514 6.47164V25.0309C42.5514 28.3513 38.5674 30.0478 36.1737 27.7465L33.8778 25.5395C33.7233 25.3911 33.5172 25.308 33.303 25.308H27.8639V14.0859C27.8639 12.3584 26.4635 10.958 24.7361 10.958H12.1973V6.47164ZM18.2626 3.34375C16.5352 3.34375 15.1348 4.74415 15.1348 6.47164V8.0205H24.7361C28.0858 8.0205 30.8014 10.7361 30.8014 14.0859V22.3705H33.303C34.2763 22.3705 35.212 22.7473 35.9137 23.422L38.2094 25.629C38.7366 26.1356 39.6139 25.7622 39.6139 25.0309V6.47164C39.6139 4.74415 38.2135 3.34375 36.4861 3.34375H18.2626Z"
                            />
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M8.48306 22.674C8.89321 21.9741 9.79303 21.7393 10.4929 22.1494C12.6363 23.4057 14.2137 23.9121 15.6694 23.9062C17.1257 23.9002 18.6718 23.3808 20.7535 22.1518C21.4521 21.7395 22.3527 21.9714 22.7651 22.6699C23.1774 23.3685 22.9455 24.2691 22.2469 24.6815C19.9563 26.0338 17.8805 26.8347 15.6814 26.8437C13.4817 26.8525 11.3704 26.0686 9.00762 24.6839C8.30777 24.2736 8.07292 23.374 8.48306 22.674Z"
                            />
                        </svg>
                        <h4 class="dark:group-hover:text-background max-sm:text-center">@lang('External Chatbot')</h4>
                        <a
                            class="absolute inset-0"
                            href="{{ \App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot') ? route('dashboard.chatbot.index') : '#' }}"
                        ></a>
                    </x-card>
                @endif

                @if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('social-media') || $app_is_demo)
                    <x-card
                        class="group w-full cursor-pointer transition-all duration-300 hover:bg-gradient-to-r hover:from-[#F0E7FF] hover:to-[#F1F5FF] lg:w-[48%]"
                        class:body="flex flex-col justify-between max-sm:gap-5 gap-16"
                        size="sm"
                    >
                        <svg
                            class="fill-foreground dark:group-hover:fill-background max-sm:mx-auto"
                            width="36"
                            height="36"
                            viewBox="0 0 36 36"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M15.0934 0.941567C15.3142 0.445008 15.8066 0.125 16.35 0.125C19.8432 0.125 22.675 2.95679 22.675 6.45V11.675H31.1928C32.5612 11.662 33.8669 12.2493 34.7651 13.2822C35.6651 14.3172 36.0653 15.6951 35.8594 17.0513L35.859 17.0535L33.5824 31.9013C33.5824 31.9009 33.5824 31.9014 33.5824 31.9013C33.233 34.2028 31.2431 35.8972 28.9158 35.875H9.75C8.99061 35.875 8.375 35.2594 8.375 34.5V16.35C8.375 16.1577 8.41537 15.9674 8.49351 15.7916L15.0934 0.941567ZM17.1985 2.97631L11.125 16.6419V33.125H28.9386C29.8983 33.1358 30.7197 32.4379 30.8636 31.4888L33.1406 16.6388C33.2249 16.0807 33.0603 15.5125 32.6899 15.0867C32.3194 14.6604 31.7802 14.4186 31.2156 14.425H31.2H21.3C20.5406 14.425 19.925 13.8094 19.925 13.05V6.45C19.925 4.76797 18.7634 3.35724 17.1985 2.97631Z"
                            />
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M5.35566 14.7918H9.75C10.5094 14.7918 11.125 15.4074 11.125 16.1668V34.4996C11.125 35.259 10.5094 35.875 9.75 35.875L5.35574 35.8746C2.7311 35.9146 0.489448 33.9866 0.137411 31.384C0.129143 31.3228 0.125 31.2612 0.125 31.1996V19.6496C0.125 19.588 0.129143 19.5264 0.137411 19.4653C0.477055 16.9544 2.64255 14.7511 5.35566 14.7918ZM2.875 19.7519V31.0967C3.07547 32.28 4.11212 33.1462 5.32019 33.1248L5.3445 33.1244L8.375 33.1246V17.5418H5.32017C4.19749 17.522 3.08805 18.4761 2.875 19.7519Z"
                            />
                        </svg>
                        <h4 class="dark:group-hover:text-background max-sm:text-center">@lang('Social Media Post')</h4>
                        <a
                            class="absolute inset-0"
                            href="{{ \App\Helpers\Classes\MarketplaceHelper::isRegistered('social-media') ? route('dashboard.user.social-media.index') : '#' }}"
                        ></a>
                    </x-card>
                @endif

                @if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('openai-realtime-chat') || $app_is_demo)
                    <x-card
                        class="group w-full cursor-pointer transition-all duration-300 hover:bg-gradient-to-r hover:from-[#F0E7FF] hover:to-[#F1F5FF] lg:w-[48%]"
                        class:body="flex flex-col justify-between max-sm:gap-5 gap-16"
                        size="sm"
                    >
                        <svg
                            class="fill-foreground dark:group-hover:fill-background max-sm:mx-auto"
                            width="40"
                            height="40"
                            viewBox="0 0 40 40"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M8.75 10.8333C8.75 6.92132 11.9213 3.75 15.8333 3.75C19.7453 3.75 22.9167 6.92132 22.9167 10.8333C22.9167 14.7453 19.7453 17.9167 15.8333 17.9167C11.9213 17.9167 8.75 14.7453 8.75 10.8333ZM15.8333 6.25C13.302 6.25 11.25 8.30203 11.25 10.8333C11.25 13.3646 13.302 15.4167 15.8333 15.4167C18.3647 15.4167 20.4167 13.3646 20.4167 10.8333C20.4167 8.30203 18.3647 6.25 15.8333 6.25Z"
                            />
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M26.9414 5.54862C27.5589 5.23989 28.3097 5.49017 28.6184 6.10764L27.5004 6.66665C28.6184 6.10764 28.6182 6.10712 28.6184 6.10764L28.6195 6.10975L28.6207 6.11224L28.6237 6.11822L28.6315 6.13442L28.6547 6.18339C28.6732 6.22329 28.6977 6.27769 28.7269 6.34585C28.785 6.48212 28.8617 6.67387 28.9452 6.91532C29.1122 7.39745 29.3084 8.08252 29.4387 8.9229C29.6999 10.6055 29.699 12.9269 28.6574 15.4733C28.3959 16.1122 27.666 16.4183 27.027 16.1569C26.388 15.8955 26.082 15.1657 26.3434 14.5267C27.1767 12.4897 27.1759 10.6445 26.9682 9.30625C26.8644 8.6362 26.7089 8.09732 26.5829 7.7331C26.5199 7.55137 26.4647 7.41435 26.4277 7.32762C26.4092 7.2843 26.3952 7.25365 26.3872 7.2364L26.3799 7.22069L26.3807 7.22245"
                            />
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M32.7275 2.24066C33.3309 1.9054 34.0919 2.12281 34.4272 2.7263L33.3345 3.33335C34.4272 2.7263 34.4269 2.72575 34.4272 2.7263L34.4285 2.72863L34.4302 2.73158L34.4344 2.73938L34.4469 2.76243C34.4569 2.78108 34.4702 2.80631 34.4865 2.83796C34.5192 2.90126 34.5639 2.99026 34.6177 3.1037C34.7252 3.33045 34.8697 3.65541 35.0285 4.06823C35.3457 4.89293 35.722 6.07405 35.9725 7.52721C36.4745 10.4386 36.472 14.4517 34.4725 18.8507C34.1869 19.4792 33.4457 19.757 32.8172 19.4713C32.1887 19.1857 31.9109 18.4445 32.1965 17.8162C33.947 13.965 33.9445 10.4781 33.5089 7.95198C33.2907 6.68641 32.9639 5.66441 32.6952 4.96568C32.561 4.61676 32.4419 4.34993 32.359 4.17533C32.3175 4.08806 32.2854 4.024 32.2649 3.98438L32.2434 3.94351L32.24 3.93718C32.2397 3.93665 32.2399 3.93691 32.24 3.93718L32.2409 3.9387C31.9064 3.33536 32.1244 2.57575 32.7275 2.24066Z"
                            />
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M13.9457 22.0834H17.7223C19.5425 22.0834 20.985 22.0834 22.1478 22.1784C23.3366 22.2755 24.3428 22.4782 25.2613 22.9462C26.751 23.7052 27.9622 24.9164 28.7211 26.406C29.1891 27.3245 29.3918 28.3307 29.489 29.5195C29.584 30.6824 29.584 32.1247 29.584 33.945V35C29.584 35.6904 29.0243 36.25 28.334 36.25H3.33398C2.64363 36.25 2.08398 35.6904 2.08398 35V33.945C2.08397 32.1249 2.08397 30.6824 2.17897 29.5195C2.2761 28.3307 2.47878 27.3245 2.94685 26.406C3.70583 24.9164 4.91693 23.7052 6.40655 22.9462C7.32518 22.4782 8.33125 22.2755 9.52024 22.1784C10.6829 22.0834 12.1254 22.0834 13.9457 22.0834ZM7.5415 25.1737C6.52232 25.693 5.69368 26.5217 5.17437 27.5409C4.9158 28.0484 4.75517 28.6889 4.67067 29.7232C4.58878 30.7252 4.58423 31.9932 4.584 33.75H27.084C27.0837 31.9932 27.0792 30.7252 26.9973 29.7232C26.9128 28.6889 26.7521 28.0484 26.4937 27.5409C25.9743 26.5217 25.1457 25.693 24.1265 25.1737M7.5415 25.1737C8.04898 24.9152 8.68944 24.7545 9.7238 24.67C10.7728 24.5844 12.1132 24.5834 14.0007 24.5834H17.6673C19.5548 24.5834 20.8951 24.5844 21.9441 24.67C22.9785 24.7545 23.619 24.9152 24.1265 25.1737"
                            />
                        </svg>
                        <h4 class="dark:group-hover:text-background max-sm:text-center">@lang('Voice Chat')</h4>
                        <a
                            class="absolute inset-0"
                            href="{{ \App\Helpers\Classes\MarketplaceHelper::isRegistered('openai-realtime-chat') ? route('dashboard.user.openai.chat.chat', ['ai_realtime_voice_chat']) : '#' }}"
                        ></a>
                    </x-card>
                @endif

                <x-card
                    class="group w-full cursor-pointer transition-all duration-300 hover:bg-gradient-to-r hover:from-[#F0E7FF] hover:to-[#F1F5FF] lg:w-[48%]"
                    class:body="flex flex-col justify-between max-sm:gap-5 gap-16"
                    size="sm"
                >
                    <svg
                        class="fill-foreground dark:group-hover:fill-background max-sm:mx-auto"
                        width="31"
                        height="31"
                        viewBox="0 0 31 31"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M24.6635 3.96639C24.3344 3.75377 23.9026 3.79943 23.6242 4.07909L4.58892 23.1979C4.15472 23.6341 4.10559 23.6958 4.07362 23.7473C4.02707 23.8224 3.99207 23.9042 3.96995 23.9902C3.95461 24.0499 3.94393 24.1292 3.92872 24.7455L3.86907 27.1625L6.35305 27.1665C6.99083 27.1675 7.07051 27.1582 7.12959 27.144C7.21681 27.123 7.30042 27.0883 7.3773 27.0411C7.42984 27.0088 7.49359 26.9581 7.94439 26.5054L26.8639 7.50281C26.9155 7.45095 26.9229 7.44332 26.9271 7.43876C27.1991 7.15174 27.2443 6.71358 27.0345 6.37553C27.0311 6.37015 27.0254 6.36118 26.9852 6.29987L26.9359 6.22462C26.3431 5.32054 25.569 4.55143 24.6635 3.96639ZM21.5573 2.02122C22.8059 0.767056 24.7592 0.555685 26.2464 1.5166C27.4941 2.32278 28.5597 3.38185 29.3751 4.62547L29.4314 4.71132C29.46 4.755 29.4874 4.79691 29.5125 4.83729C30.4206 6.3001 30.2291 8.19432 29.0445 9.44471C29.0118 9.47918 28.9765 9.51465 28.9398 9.55163L10.0113 28.5633C9.98632 28.5884 9.96139 28.6134 9.93651 28.6385C9.61063 28.9666 9.29159 29.2879 8.90493 29.5256C8.56626 29.7339 8.19679 29.8873 7.81013 29.98C7.36843 30.0861 6.91572 30.0847 6.45432 30.0834C6.41906 30.0833 6.38375 30.0831 6.34839 30.0831L2.37202 30.0767C1.97942 30.0761 1.60364 29.9172 1.32971 29.636C1.05576 29.3547 0.906776 28.975 0.91646 28.5825L1.01294 24.6735C1.01379 24.6395 1.01459 24.6054 1.01538 24.5715C1.02585 24.1241 1.03606 23.6878 1.14533 23.2632C1.24104 22.8914 1.39266 22.5361 1.59512 22.2097C1.82637 21.837 2.13465 21.5281 2.45003 21.2122C2.47399 21.1882 2.49799 21.1641 2.52201 21.1401L21.5573 2.02122Z"
                        />
                    </svg>
                    <h4 class="dark:group-hover:text-background max-sm:text-center">@lang('Blog Post')</h4>
                    <a
                        class="absolute inset-0"
                        href="{{ route('dashboard.user.openai.articlewizard.new') }}"
                    ></a>
                </x-card>
            </div>
        </x-card>
        {{-- end: add new --}}

        {{-- begin: social media posts --}}
        @includeIf('social-media::theme.social-media-post-default-theme')
        {{-- end: social media posts --}}

        {{-- begin: recently launched --}}
        <x-card
            class="w-full"
            id="recent"
            size="md"
        >
            <x-slot:head
                class="border-0 pb-0 pt-5"
            >
                <div class="flex items-center justify-between">
                    <h4 class="m-0 text-[17px]">{{ __('Recently Launched') }}</h4>
                    <x-button
                        variant="link"
                        href="{{ route('dashboard.user.openai.documents.all') }}"
                    >
                        <span class="text-nowrap font-bold text-foreground"> {{ __('View All') }} </span>
                        <x-tabler-chevron-right class="ms-auto size-4" />
                    </x-button>
                </div>
            </x-slot:head>

            <div
                class="lqd-docs-container group"
                data-view-mode="grid"
            >
                <div class="lqd-docs-list grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                    @foreach ($recently_launched as $entry)
                        @if ($entry->generator != null)
                            <x-documents.item
                                :$entry
                                style="extended"
                                trim="100"
                                hide-fav
                            />
                        @endif
                    @endforeach
                </div>
            </div>
        </x-card>
        {{-- end: recently launched --}}

        {{-- begin: favorite templates --}}
        <x-card
            class="w-full"
            id="templates"
            size="md"
        >
            <x-slot:head
                class="border-0 pb-0 pt-5"
            >
                <div class="flex items-center justify-between">
                    <h4 class="m-0 text-[17px]">{{ __('Favorite Templates') }}</h4>
                    <x-button
                        variant="link"
                        href="{{ route('dashboard.user.openai.list') }}"
                    >
                        <span class="text-nowrap font-bold text-foreground"> {{ __('View All') }} </span>
                        <x-tabler-chevron-right class="ms-auto size-4" />
                    </x-button>
                </div>
            </x-slot:head>

            <div
                class="lqd-docs-container group"
                data-view-mode="grid"
            >
                <div class="lqd-docs-list grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                    @foreach ($favoriteOpenAis as $entry)
                        @php
                            $upgrade = false;
                            if ($entry->premium == 1 && $plan_type === 'regular') {
                                $upgrade = true;
                            }

                            if ($upgrade) {
                                $href = LaravelLocalization::localizeUrl(route('dashboard.user.payment.subscription'));
                            } elseif (isset($entry->slug) && in_array($entry->slug, ['ai_vision', 'ai_ai_chat_image', 'ai_code_generator', 'ai_youtube', 'ai_pdf'])) {
                                $href = LaravelLocalization::localizeUrl(route('dashboard.user.openai.generator.workbook', $entry->slug));
                            } else {
                                $href = LaravelLocalization::localizeUrl(route('dashboard.user.openai.generator', $entry->slug));
                            }
                        @endphp
                        @if ($upgrade || $entry->active == 1)
                            <a
                                class="lqd-posts-item relative flex w-full flex-col flex-wrap items-start gap-3 border-b p-4 text-xs transition-all last:border-none hover:bg-foreground/5"
                                href="{{ $href }}"
                            >
                            @else
                                <p class="lqd-posts-item relative flex w-full flex-col flex-wrap items-start gap-3 border-b p-4 text-xs transition-all last:border-none">
                        @endif
                        <x-lqd-icon
                            size="lg"
                            style="background: {{ $entry->color }}"
                            active-badge
                            active-badge-condition="{{ $entry->active == 1 }}"
                        >
                            <span class="flex size-5">
                                @if ($entry->image !== 'none')
                                    {!! html_entity_decode($entry->image) !!}
                                @endif
                            </span>
                        </x-lqd-icon>
                        <span class="w-full grow">
                            <span class="lqd-fav-temp-item-title block text-sm font-medium">
                                {{ __($entry->title) }}
                            </span>
                            <div class="lqd-posts-item-content-inner h-full">
                                <span class="lqd-fav-temp-item-desc line-clamp-4 max-w-full text-ellipsis italic opacity-45">
                                    {{ str()->words(__($entry->description)) }}
                                </span>
                            </div>
                        </span>
                        @if ($upgrade)
                            <span class="absolute inset-0 flex items-center justify-center bg-background/50">
                                <x-badge
                                    class="rounded-md py-1.5"
                                    variant="info"
                                >
                                    {{ __('Upgrade') }}
                                </x-badge>
                            </span>
                        @endif
                        @if ($upgrade || $entry->active == 1)
                            </a>
                        @else
                            </p>
                        @endif
                        @if ($loop->iteration == 4)
                            @break
                        @endif
                    @endforeach
                </div>
            </div>
        </x-card>
        {{-- end: favorite templates --}}

        @includeFirst(['anouncement::partials.dashboard', 'vendor.empty'])

        {{-- begin: submit ticket --}}
        <x-card
            class="flex w-full flex-col justify-center lg:w-[48%]"
            id="submit-ticket"
            size="lg"
        >
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-3">
                    <div class="inline-grid size-36 items-center justify-center rounded-full bg-[#F9F9F9] dark:bg-foreground/5">
                        <img
                            src="{{ asset('images/icons/submit-ticket.png') }}"
                            alt=""
                        >
                    </div>
                    <div class="flex flex-col items-center">
                        <h3 class="text-center">@lang('Have a question?')</h3>
                        <h3 class="text-center text-heading-foreground/50">@lang('We’re here to help you.')</h3>
                    </div>
                </div>
                <x-button
                    class="mx-auto w-fit text-[14px] text-heading-foreground hover:bg-primary"
                    variant="ghost-shadow"
                    href="{{ route('dashboard.support.list') }}"
                >
                    <x-tabler-plus class="size-3.5" />
                    {{ __('Submit a Ticket') }}
                </x-button>
            </div>
        </x-card>
        {{-- end: submit ticket --}}
    </div>
@endsection

@push('script')
    @if ($app_is_not_demo)
        @includeFirst(['onboarding::include.introduction', 'panel.admin.onboarding.include.introduction', 'vendor.empty'])
        @includeFirst(['onboarding-pro::include.introduction', 'panel.admin.onboarding-pro.include.introduction', 'vendor.empty'])
    @endif
    @if (Route::has('dashboard.user.dash_notify_seen'))
        <script>
            function dismiss() {
                // localStorage.setItem('lqd-announcement-dismissed', true);
                document.querySelector('.lqd-announcement').style.display = 'none';
                $.ajax({
                    url: '{{ route('dashboard.user.dash_notify_seen') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        /* console.log(response); */
                    }
                });
            }
        </script>
    @endif
@endpush
