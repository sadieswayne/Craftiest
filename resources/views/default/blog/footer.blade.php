<footer class="site-footer relative bg-black pb-11 pt-40 text-white">
    <div
        class="absolute inset-0"
        style="background: radial-gradient(circle at 0% -20%, #a12a91, rgba(33, 13, 123, 0.83), transparent, transparent, transparent)"
    >
    </div>
    <div class="absolute inset-x-0 -top-px">
        <svg
            class="w-full fill-background"
            preserveAspectRatio="none"
            width="1440"
            height="86"
            viewBox="0 0 1440 86"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path d="M0 85.662C240 29.1253 480 0.857 720 0.857C960 0.857 1200 29.1253 1440 85.662V0H0V85.662Z" />
        </svg>
    </div>
    <div class="relative">
        <div class="container mb-28">
            <div class="mx-auto w-1/2 text-center max-lg:w-10/12 max-sm:w-full">
                <p class="mb-9 text-[10px] font-semibold uppercase tracking-widest"><span
                        class="!me-2 inline-block rounded-xl bg-[#262626] px-3 py-1">{{ __($setting->site_name) }}</span>
                    {{ __($fSetting->footer_text_small) }}</p>
                <p
                    class="font-oneset -from-[5%] mb-8 bg-gradient-to-br from-transparent to-white to-50% bg-clip-text text-[100px] font-bold leading-none tracking-tight text-transparent max-sm:text-[18vw]">
                    {{ __($fSetting->footer_header) }}</p>
                <p class="font-oneset mb-9 px-10 text-[20px] font-normal leading-[1.25em] opacity-50">
                    {{ __($fSetting->footer_text) }}</p>
                <a
                    class="relative inline-flex items-center overflow-hidden rounded-xl border-[2px] border-white border-opacity-0 bg-white/10 px-7 py-4 font-semibold transition-all duration-300 hover:scale-105 hover:border-white hover:bg-white hover:bg-opacity-100 hover:text-black hover:shadow-lg"
                    href="{{ !empty($fSetting->footer_button_url) ? $fSetting->footer_button_url : '#' }}"
                    target="_blank"
                >
                    {!! __($fSetting->footer_button_text) !!}
                    <span class="relative z-10 ms-2 inline-flex items-center">
                        <svg
                            width="11"
                            height="14"
                            viewBox="0 0 47 62"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M27.95 0L0 38.213H18.633V61.141L46.583 22.928H27.95V0Z" />
                        </svg>
                    </span>
                </a>
                <x-button
                    link="{{ $fSetting->footer_button_url }}"
                    label="{{ __($fSetting->footer_button_text) }}"
                    size="lg"
                    iconPos="end"
                    bg="bg-white bg-opacity-10 border-[2px] border-white border-opacity-0 hover:bg-white hover:bg-opacity-100"
                    text="hover:!text-black"
                >
                    <x-slot name="icon">
                        <svg
                            class="ml-2"
                            width="11"
                            height="14"
                            viewBox="0 0 47 62"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M27.95 0L0 38.213H18.633V61.141L46.583 22.928H27.95V0Z" />
                        </svg>
                    </x-slot>
                </x-button>
            </div>
        </div>
        <hr class="border-white border-opacity-15">
        <div class="container">
            <div class="flex flex-wrap items-center justify-between gap-8 pb-7 pt-10 max-sm:justify-center">
                <a href="{{ route('index') }}">
                    @if (isset($setting->logo_2x_path))
                        <img
                            src="{{ custom_theme_url($setting->logo_path, true) }}"
                            srcset="/{{ $setting->logo_2x_path }} 2x"
                            alt="{{ $setting->site_name }} logo"
                        >
                    @else
                        <img
                            src="{{ custom_theme_url($setting->logo_path, true) }}"
                            alt="{{ $setting->site_name }} logo"
                        >
                    @endif
                </a>
                <ul class="flex flex-wrap items-center gap-7 text-[14px] max-sm:justify-center">
                    @foreach (\App\Models\SocialMediaAccounts::where('is_active', true)->get() as $social)
                        <li>
                            <a
                                class="inline-flex items-center gap-2"
                                href="{{ $social['link'] }}"
                            >
                                <span class="w-3.5 [&_svg]:h-auto [&_svg]:w-full">
                                    {!! $social['icon'] !!}
                                </span>
                                {{ $social['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <hr class="border-white border-opacity-15">
            <div class="flex flex-wrap items-center justify-center gap-4 py-9 max-sm:text-center">
                <p
                    class="!text-end text-[14px] opacity-60"
                    style="color: {{ $fSetting->footer_text_color }};"
                >
                    {{ date('Y') . ' ' . $setting->site_name . '. ' . __($fSetting->footer_copyright) }}
                </p>
            </div>
        </div>
    </div>
</footer>

@if ($setting->gdpr_status == 1)
    <div
        class="fixed bottom-12 left-1/2 z-50 -translate-x-1/2 rounded-full bg-white p-2 drop-shadow-2xl max-sm:w-11/12"
        id="gdpr"
    >
        <div class="flex items-center justify-between gap-6 text-sm">
            <div class="content-left pl-4">
                {!! __($setting->gdpr_content) !!}
            </div>
            <div class="content-right text-end">
                <button class="cursor-pointer rounded-full bg-black px-4 py-2 text-white">{!! __($setting->gdpr_button) !!}</button>
            </div>
        </div>
    </div>
@endif
