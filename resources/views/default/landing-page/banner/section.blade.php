<section
    class="site-section relative flex min-h-screen items-center justify-center overflow-hidden py-52 text-center text-white max-md:pb-16 max-md:pt-48"
    id="banner"
>
    <div class="absolute start-0 top-0 h-full w-full transform-gpu overflow-hidden [backface-visibility:hidden]">
        <div class="banner-bg absolute left-0 top-0 h-full w-full"></div>
    </div>
    <div class="container relative">
        <div class="mx-auto flex w-1/2 flex-col items-center max-lg:w-2/3 max-md:w-full">
            <h6
                class="relative mb-8 translate-y-6 overflow-hidden rounded-2xl bg-white bg-opacity-15 px-3 py-1 text-white opacity-0 blur-lg transition-all ease-out group-[.page-loaded]/body:translate-y-0 group-[.page-loaded]/body:opacity-100 group-[.page-loaded]/body:blur-0">
                <div class="banner-subtitle-gradient absolute -inset-3 blur-3xl transition-all duration-500 group-[.page-loaded]/body:opacity-0">
                    <div class="animate-hue-rotate absolute inset-0 bg-gradient-to-br from-violet-600 to-red-500"></div>
                </div>
                <span class="relative">{!! __($setting->site_name) !!}</span>
                <span class="dot relative"></span>
                <span class="relative opacity-60">{!! __($fSetting->hero_subtitle) !!}</span>
            </h6>
            <div class="banner-title-wrap relative">
                <h1
                    class="banner-title mb-7 translate-y-7 font-body font-bold -tracking-wide text-white opacity-0 transition-all ease-out group-[.page-loaded]/body:translate-y-0 group-[.page-loaded]/body:opacity-100">
                    {!! __($fSetting->hero_title) !!}
                    @if ($fSetting->hero_title_text_rotator != null)
                        <span class="lqd-text-rotator inline-grid grid-cols-1 grid-rows-1 transition-[width] duration-200">
                            @foreach (explode(',', __($fSetting->hero_title_text_rotator)) as $keyword)
                                <span
                                    class="lqd-text-rotator-item {{ $loop->first ? 'lqd-is-active' : '' }} col-start-1 row-start-1 inline-flex translate-x-3 opacity-0 blur-sm transition-all duration-300 [&.lqd-is-active]:translate-x-0 [&.lqd-is-active]:opacity-100 [&.lqd-is-active]:blur-0"
                                >
                                    <span>{!! $keyword !!}</span>
                                </span>
                            @endforeach
                        </span>
                    @endif
                    <svg
                        class="lqd-split-text-words inline transition-all duration-[2850ms]"
                        width="47"
                        height="62"
                        viewBox="0 0 47 62"
                        fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path d="M27.95 0L0 38.213H18.633V61.141L46.583 22.928H27.95V0Z" />
                    </svg>
                </h1>
            </div>
            <p
                class="mb-7 w-3/4 text-[20px] leading-[1.25em] text-fuchsia-700 opacity-75 max-sm:w-full [&_.lqd-split-text-words]:translate-y-3 [&_.lqd-split-text-words]:opacity-0 [&_.lqd-split-text-words]:transition-all [&_.lqd-split-text-words]:ease-out group-[.page-loaded]/body:[&_.lqd-split-text-words]:translate-y-0 group-[.page-loaded]/body:[&_.lqd-split-text-words]:text-white group-[.page-loaded]/body:[&_.lqd-split-text-words]:opacity-100">
                <x-split-words
                    text="{!! __($fSetting->hero_description) !!}"
                    transitionDelayStart="{{ 0.15 }}"
                    transitionDelayStep="{{ 0.02 }}"
                />
            </p>
            <div class="translate-y-3 opacity-0 transition-all delay-[450ms] group-[.page-loaded]/body:translate-y-0 group-[.page-loaded]/body:opacity-100">
                @if ($fSetting->hero_button_type == 1)
                    <a
                        class="relative inline-flex items-center overflow-hidden rounded-xl border-[2px] border-[#343C57] border-opacity-0 bg-white bg-opacity-10 px-7 py-4 font-semibold transition-all duration-300 hover:scale-105 hover:border-white hover:bg-white hover:bg-opacity-100 hover:text-black hover:shadow-lg"
                        href="{{ !empty($fSetting->hero_button_url) ? $fSetting->hero_button_url : '#' }}"
                    >
                        <span class="relative z-10 inline-flex items-center">
                            {!! __($fSetting->hero_button) !!}
                            <svg
                                class="ml-2"
                                width="11"
                                height="14"
                                viewBox="0 0 47 62"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path d="M27.95 0L0 38.213H18.633V61.141L46.583 22.928H27.95V0Z"></path>
                            </svg>
                        </span>
                    </a>
                @else
                    <a
                        class="inline-flex w-full items-center justify-center bg-black bg-opacity-10 px-4 py-3 text-lg font-semibold text-white transition-all duration-300 hover:bg-opacity-20"
                        data-fslightbox="video-gallery"
                        style="border-radius: 3rem;"
                        href="{{ !empty($fSetting->hero_button_url) ? $fSetting->hero_button_url : '#' }}"
                    >
                        <svg
                            class="icon icon-tabler icon-tabler-player-play-filled me-4 bg-white"
                            style="padding: 13px; border-radius: 2rem;"
                            xmlns="http://www.w3.org/2000/svg"
                            width="40"
                            height="40"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                stroke="none"
                                d="M0 0h24v24H0z"
                                fill="none"
                            ></path>
                            <path
                                d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z"
                                stroke-width="0"
                                fill="#37393d"
                            ></path>
                        </svg>
                        {!! __($fSetting->hero_button) !!} &nbsp;
                    </a>
                @endif
            </div>
            <br>
            <div class="translate-y-3 opacity-0 transition-all delay-[500ms] group-[.page-loaded]/body:translate-y-0 group-[.page-loaded]/body:opacity-100">
                <a
                    class="opacity-50 transition-opacity hover:opacity-100"
                    href="#features"
                >{!! __($fSetting->hero_scroll_text) !!}</a>
            </div>
        </div>
    </div>
    <div class="banner-divider absolute inset-x-0 -bottom-[2px]">
        <svg
            class="h-auto w-full fill-background"
            width="1440"
            height="105"
            viewBox="0 0 1440 105"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            preserveAspectRatio="none"
        >
            <path d="M0 0C240 68.7147 480 103.072 720 103.072C960 103.072 1200 68.7147 1440 0V104.113H0V0Z" />
        </svg>
    </div>
</section>
