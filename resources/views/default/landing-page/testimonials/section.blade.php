    {!! adsense_testimonials_728x90() !!}
    <section
        class="site-section relative py-10 transition-all duration-700 md:translate-y-8 md:opacity-0 [&.lqd-is-in-view]:translate-y-0 [&.lqd-is-in-view]:opacity-100"
        id="testimonials"
    >
        <div
            class="absolute inset-x-0 top-0 -z-1 h-[150vh]"
            style="background: linear-gradient(to bottom, transparent, #F0EFFA, transparent)"
        ></div>
        <div class="container relative">
            <div
                class="rounded-[50px] border bg-contain bg-center bg-no-repeat p-11 pb-24 max-sm:px-5"
                style="background-image: url({{ custom_theme_url('assets/img/landing-page/world-map.png') }})"
            >
                <x-section-header
                    width="w-1/2"
                    mb="10"
                    title="{!! $fSectSettings->testimonials_title !!}"
                    subtitle=""
                >
                    <h6 class="mb-6 inline-block rounded-md bg-[#28027C] bg-opacity-15 px-3 py-1 text-[13px] font-medium text-[#28027C]">
                        {!! __($fSectSettings->testimonials_subtitle_one) !!}
                        <span class="dot"></span>
                        <span class="opacity-50">{!! __($fSectSettings->testimonials_subtitle_two) !!}</span>
                    </h6>
                </x-section-header>
                <div class="max-lg:11/12 mx-auto w-8/12 max-md:w-full">
                    <div class="mb-20">
                        <div
                            class="mx-auto mb-7 w-[235px] gap-5"
                            data-flickity='{ "asNavFor": ".testimonials-main-carousel", "contain": false, "pageDots": false, "cellAlign": "center", "prevNextButtons": false, "wrapAround": true, "draggable": false }'
                            style="mask-image: linear-gradient(to right, transparent 0%, #000 15%, #000 85%, transparent 100% ); -webkit-mask-image: linear-gradient(to right, transparent 0%, #000 15%, #000 85%, transparent 100% );"
                        >
                            @foreach ($testimonials as $item)
                                @include('landing-page.testimonials.item-image')
                            @endforeach
                        </div>
                        <div
                            class="testimonials-main-carousel text-center text-[26px] leading-[1.27em] text-heading-foreground max-sm:text-lg max-sm:[&_.flickity-button-icon]:!left-1/4 max-sm:[&_.flickity-button-icon]:!top-1/4 max-sm:[&_.flickity-button-icon]:!h-1/2 max-sm:[&_.flickity-button-icon]:!w-1/2 [&_.flickity-button.next]:-right-16 max-md:[&_.flickity-button.next]:-right-10 [&_.flickity-button.previous]:-left-16 max-md:[&_.flickity-button.previous]:-left-10 [&_.flickity-button]:opacity-40 [&_.flickity-button]:transition-all [&_.flickity-button]:hover:bg-transparent [&_.flickity-button]:hover:opacity-100 [&_.flickity-button]:focus:shadow-none max-sm:[&_.flickity-button]:relative max-sm:[&_.flickity-button]:!left-auto max-sm:[&_.flickity-button]:!right-auto max-sm:[&_.flickity-button]:top-auto max-sm:[&_.flickity-button]:!mx-4 max-sm:[&_.flickity-button]:translate-y-0"
                            data-flickity='{ "contain": true, "wrapAround": true, "pageDots": false, "adaptiveHeight": true }'
                        >
                            @foreach ($testimonials as $item)
                                @include('landing-page.testimonials.item-quote')
                            @endforeach

                        </div>
                    </div>
                    <div class="flex justify-center gap-20 opacity-80 max-lg:gap-12 max-sm:gap-4">
                        @foreach ($clients as $entry)
                            <img
                                class="h-full w-full object-cover object-center"
                                style="max-width: 48px; max-height: 48px;"
                                src="{{ url('') . isset($entry->avatar) ? (str_starts_with($entry->avatar, 'asset') ? custom_theme_url($entry->avatar) : '/clientAvatar/' . $entry->avatar) : custom_theme_url('assets/img/auth/default-avatar.png') }}"
                                alt="{{ __($entry->alt) }}"
                                title="{{ __($entry->title) }}"
                            >
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
