@php
    $premium_features = [
        'VIP Support',
        'Access to All Extensions - <span class="font-bold text-[#6977DE]">worth $900+</span>',
        'Access to All Themes - <span class="font-bold text-[#6977DE]">worth $390</span>',
        '5 Hours of Customization',
        'Direct access to Our Development Team',
    ];
@endphp

@extends('vendor.installer.layouts.master', ['stepShow' => false])

@section('template_title')
    {{ trans('Marketplace Subscription') }}
@endsection

@section('title')
    {{ trans('installer_messages.welcome.title') }}
@endsection

@section('style')
    <style>
        body {
            background-image: url({{ custom_theme_url('assets/img/misc/promo-bg-1.jpg') }});
            background-size: 100%;
            background-position: top center;
            background-repeat: no-repeat;
        }
    </style>
@endsection

@section('content')
    <header class="fixed inset-x-0 top-0 z-5 w-full border-b border-black/5 py-5 shadow-[0_4px_14px_rgba(0,0,0,0.05)] backdrop-blur-md">
        <nav class="flex items-center justify-between px-5">
            <div class="hidden basis-1/3 md:flex">

            </div>
            <div class="flex basis-1/2 justify-start md:justify-center">
                @if (isset($setting->logo_dashboard))
                    <img
                        class="h-auto"
                        src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                        @if (isset($setting->logo_dashboard_2x_path) && !empty($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                @else
                    <img
                        class="h-auto"
                        src="{{ custom_theme_url($setting->logo_path, true) }}"
                        @if (isset($setting->logo_2x_path) && !empty($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                @endif
            </div>
            <div class="flex basis-1/2 justify-end md:basis-1/3">
                <a
                    class="inline-flex items-center gap-1 text-2xs opacity-65 transition-opacity hover:opacity-100"
                    href="/dashboard"
                >
                    {{ trans('Skip This Offer') }}
                    <x-tabler-chevron-right class="size-4" />
                </a>
            </div>
        </nav>
    </header>

    <div class="flex min-h-screen flex-col justify-center">
        <div class="container pb-20 pt-36">
            <div class="grid grid-cols-12 gap-8">

                <x-card
                    class="relative col-span-full flex items-center border-4 border-heading-foreground/5 lg:col-span-5"
                    class:body="static rounded-[inherit] only:grow-0 lg:p-10 w-full"
                >
                    <x-outline-glow
                        class="[--glow-color-primary:238deg_71%_79%] [--glow-color-secondary:166deg_74%_45%] [--outline-glow-iteration:200] [--outline-glow-w:4px]"
                        effect="3"
                    />

                    <div class="mb-6 inline-grid size-14 place-content-center rounded-xl bg-gradient-to-br from-[#82E2F4] via-[#8A8AED] to-[#6977DE] text-white">
                        <x-tabler-diamond
                            class="size-10"
                            stroke-width="1.5"
                        />
                    </div>
                    <h1 class="mb-6 text-4xl">
                        {{ trans('Start Your ') }}
                        <span class="block opacity-50">
                            {{ trans('Premium Membership.') }}
                        </span>
                    </h1>
                    <ul class="mb-11 space-y-4 self-center text-xs font-medium text-heading-foreground">
                        @foreach ($premium_features as $feature)
                            <li>
                                <svg
                                    class="me-3.5 inline align-middle"
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
                                {!! $feature !!}
                            </li>
                        @endforeach
                    </ul>

                    @if ($data)
                        <a
                            class="group flex w-full items-center justify-center gap-3 rounded-full bg-background px-4 py-5 text-center text-[18px] font-bold shadow-[0_14px_44px_#2D2C6A17] transition-all hover:-translate-y-1 hover:scale-[1.025] hover:bg-gradient-to-br hover:from-[#82E2F4] hover:via-[#8A8AED] hover:to-[#6977DE] hover:shadow-2xl hover:shadow-black/10"
                            href="{{ '/dashboard' }}"
                        >
                            <span
                                class="inline-block bg-gradient-to-br from-[#82E2F4] via-[#8A8AED] to-[#6977DE] bg-clip-text text-transparent group-hover:from-white group-hover:via-white group-hover:to-white"
                            >
                                {{ trans('Subscription activated') }}
                            </span>
                            <x-tabler-chevron-right class="size-4 text-[#6977DE] group-hover:text-white" />
                        </a>
                    @else
                        <a
                            class="group flex w-full items-center justify-center gap-3 rounded-full bg-background px-4 py-5 text-center text-[18px] font-bold shadow-[0_14px_44px_#2D2C6A17] transition-all hover:-translate-y-1 hover:scale-[1.025] hover:bg-gradient-to-br hover:from-[#82E2F4] hover:via-[#8A8AED] hover:to-[#6977DE] hover:shadow-2xl hover:shadow-black/10"
                            href="{{ $payment }}"
                        >
                            <span
                                class="inline-block bg-gradient-to-br from-[#82E2F4] via-[#8A8AED] to-[#6977DE] bg-clip-text text-transparent group-hover:from-white group-hover:via-white group-hover:to-white"
                            >
                                {{ trans('Join the VIP Program') }}
                            </span>
                            <x-tabler-chevron-right class="size-4 text-[#6977DE] group-hover:text-white" />
                        </a>
                    @endif
                </x-card>

                <div class="col-span-full md:col-span-4 lg:col-start-6 xl:col-span-3 xl:col-start-7">
                    <h6 class="mb-6 inline-block rounded-full bg-[#F5FAFF] px-4 py-1 font-body text-sm font-semibold">
                        <span class="inline-block bg-gradient-to-br from-[#82E2F4] via-[#8A8AED] to-[#6977DE] bg-clip-text text-transparent">
                            {{ trans('Included Extensions') }}
                        </span>
                    </h6>

                    <ul class="flex w-full flex-col gap-2.5">
                        @foreach ($paidExtensions as $item)
                            <li class="flex items-center gap-3.5">
                                <svg
                                    width="16"
                                    height="15"
                                    viewBox="0 0 16 15"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M2.09635 6.87072C1.80296 6.87154 1.51579 6.95542 1.26807 7.11264C1.02035 7.26986 0.822208 7.494 0.696564 7.75914C0.570919 8.02427 0.522908 8.31956 0.558084 8.61084C0.59326 8.90212 0.710186 9.17749 0.895335 9.4051L4.84228 14.2401C4.98301 14.4148 5.1634 14.5535 5.36847 14.6445C5.57353 14.7355 5.79736 14.7763 6.02136 14.7635C6.50043 14.7377 6.93295 14.4815 7.20871 14.0601L15.4075 0.855925C15.4089 0.853735 15.4103 0.851544 15.4117 0.849387C15.4886 0.731269 15.4637 0.497192 15.3049 0.350142C15.2613 0.309761 15.2099 0.278736 15.1538 0.25898C15.0977 0.239223 15.0382 0.231153 14.9789 0.235266C14.9196 0.239379 14.8618 0.255589 14.809 0.282896C14.7562 0.310204 14.7095 0.348031 14.6719 0.394048C14.669 0.397666 14.6659 0.40123 14.6628 0.404739L6.39421 9.74702C6.36275 9.78257 6.32454 9.81152 6.28179 9.83218C6.23905 9.85283 6.19263 9.86479 6.14522 9.86736C6.09782 9.86992 6.05038 9.86304 6.00565 9.84711C5.96093 9.83119 5.91982 9.80653 5.88471 9.77458L3.14051 7.27735C2.8555 7.01608 2.48299 6.87102 2.09635 6.87072Z"
                                        fill="url(#paint0_linear_6413_808)"
                                    />
                                    <defs>
                                        <linearGradient
                                            id="paint0_linear_6413_808"
                                            x1="0.546875"
                                            y1="3.19866"
                                            x2="12.7738"
                                            y2="14.2613"
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
                                {!! trans($item['name']) !!}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-span-full md:col-span-3 xl:col-span-2">
                    <h6 class="mb-6 inline-block rounded-full bg-[#F5FAFF] px-4 py-1 font-body text-sm font-semibold">
                        <span class="inline-block bg-gradient-to-br from-[#82E2F4] via-[#8A8AED] to-[#6977DE] bg-clip-text text-transparent">
                            {{ trans('Included Themes') }}
                        </span>
                    </h6>

                    <ul class="flex w-full flex-col gap-2.5">
                        @foreach ($paidThemes as $item)
                            <li class="flex items-center gap-3.5">
                                <svg
                                    width="16"
                                    height="15"
                                    viewBox="0 0 16 15"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M2.09635 6.87072C1.80296 6.87154 1.51579 6.95542 1.26807 7.11264C1.02035 7.26986 0.822208 7.494 0.696564 7.75914C0.570919 8.02427 0.522908 8.31956 0.558084 8.61084C0.59326 8.90212 0.710186 9.17749 0.895335 9.4051L4.84228 14.2401C4.98301 14.4148 5.1634 14.5535 5.36847 14.6445C5.57353 14.7355 5.79736 14.7763 6.02136 14.7635C6.50043 14.7377 6.93295 14.4815 7.20871 14.0601L15.4075 0.855925C15.4089 0.853735 15.4103 0.851544 15.4117 0.849387C15.4886 0.731269 15.4637 0.497192 15.3049 0.350142C15.2613 0.309761 15.2099 0.278736 15.1538 0.25898C15.0977 0.239223 15.0382 0.231153 14.9789 0.235266C14.9196 0.239379 14.8618 0.255589 14.809 0.282896C14.7562 0.310204 14.7095 0.348031 14.6719 0.394048C14.669 0.397666 14.6659 0.40123 14.6628 0.404739L6.39421 9.74702C6.36275 9.78257 6.32454 9.81152 6.28179 9.83218C6.23905 9.85283 6.19263 9.86479 6.14522 9.86736C6.09782 9.86992 6.05038 9.86304 6.00565 9.84711C5.96093 9.83119 5.91982 9.80653 5.88471 9.77458L3.14051 7.27735C2.8555 7.01608 2.48299 6.87102 2.09635 6.87072Z"
                                        fill="url(#paint0_linear_6413_808)"
                                    />
                                    <defs>
                                        <linearGradient
                                            id="paint0_linear_6413_808"
                                            x1="0.546875"
                                            y1="3.19866"
                                            x2="12.7738"
                                            y2="14.2613"
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
                                {!! trans($item['name']) !!}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <p class="mt-14 w-full text-center text-2xs font-medium">
                <span class="opacity-50">
                    {{ trans('Seats are limited.') }}
                </span>
                <a href="{{ $payment }}">
                    {{ trans('Learn more about') }}
                    <span class="underline">
                        {{ trans('Premium Membership') }}
                    </span>
                </a>
            </p>

            {{-- @includeWhen(is_null($portal), 'vendor.installer.magicai_c4st_Act', [
				'button' =>
					'flex items-center justify-center gap-2 rounded-xl p-2 font-medium shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-all duration-300 hover:scale-105 hover:bg-black hover:text-white',
				'target' => '',
				'return_url' => route('LaravelInstaller::license') . '?license=verified',
			])
			@includeWhen($portal, 'vendor.installer.magicai_license_token', [
				'button' =>
					'flex items-center justify-center gap-2 rounded-xl p-2 font-medium shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-all duration-300 hover:scale-105 hover:bg-black hover:text-white',
			]) --}}
        </div>
    </div>
@endsection
