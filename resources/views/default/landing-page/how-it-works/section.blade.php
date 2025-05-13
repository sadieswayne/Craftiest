    {!! adsense_how_it_works_728x90() !!}
    <section
        class="site-section py-10 transition-all duration-700 md:translate-y-8 md:opacity-0 [&.lqd-is-in-view]:translate-y-0 [&.lqd-is-in-view]:opacity-100"
        id="how-it-works"
    >
        <div class="container">
            <div
                class="rounded-[50px] bg-[#010101] bg-cover p-10 py-24 text-white text-opacity-60 shadow-xl max-sm:bg-bottom max-sm:px-5"
                style="background-image: url({{ custom_theme_url('assets/img/landing-page/steps-bg.jpg') }});"
            >
                <div class="mx-auto mb-14 w-2/5 text-center max-xl:w-1/2 max-lg:w-8/12 max-md:w-full">
                    <h2 class="text-[64px] leading-none text-[#E5E6E6] max-sm:text-[45px]">{!! __($fSectSettings->how_it_works_title) !!}
                    </h2>
                </div>
                <div class="grid-cols-{{ count($howitWorks) }} mb-20 grid gap-7 max-md:grid-cols-1">
                    @foreach ($howitWorks as $item)
                        @include('landing-page.how-it-works.item')
                    @endforeach
                </div>
                @if ($howitWorksDefaults['option'] == 1)
                    <div class="flex justify-center text-[#A2B2C9]">
                        {!! $howitWorksDefaults['html'] !!}
                    </div>
                @endif
            </div>
        </div>
    </section>
