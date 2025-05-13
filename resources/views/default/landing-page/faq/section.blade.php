{!! adsense_faq_728x90() !!}
<section
    class="site-section py-10 transition-all duration-700 md:translate-y-8 md:opacity-0 [&.lqd-is-in-view]:translate-y-0 [&.lqd-is-in-view]:opacity-100"
    id="faq"
>
    <div class="container">
        <div class="relative rounded-[50px] border p-11 pb-16 max-sm:px-5">
            <x-section-header
                mb="9"
                width="w-1/2"
                title="{!! __($fSectSettings->faq_title) !!}"
                subtitle="{!! __($fSectSettings->faq_subtitle) !!}"
            >
                <h6 class="mb-6 inline-block rounded-md bg-[#60027C] bg-opacity-15 px-3 py-1 text-[13px] font-medium text-[#60027C]">
                    {!! __($fSectSettings->faq_text_one) !!}
                    <span class="dot"></span>
                    <span class="opacity-50">{!! __($fSectSettings->faq_text_two) !!}</span>
                </h6>
            </x-section-header>
            <div class="lqd-accordion mx-auto w-5/6 max-lg:w-full">
                @foreach ($faq as $item)
                    @include('landing-page.faq.item')
                @endforeach
            </div>
        </div>
    </div>
</section>
