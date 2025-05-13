{!! adsense_features_728x90() !!}
<section
    class="site-section pb-20 pt-32 transition-all duration-700 md:translate-y-8 md:opacity-0 [&.lqd-is-in-view]:translate-y-0 [&.lqd-is-in-view]:opacity-100"
    id="features"
>
    <div class="container">
        <x-section-header
            title="{!! __($fSectSettings->features_title) !!}"
            subtitle="{!! __($fSectSettings->features_description) ?? __('MagicAI is designed to help you generate high-quality content instantly, without breaking a sweat.') !!}"
        />
        <div class="grid grid-cols-3 justify-between gap-x-20 gap-y-9 max-lg:grid-cols-2 max-lg:gap-x-10 max-md:grid-cols-1">
            @foreach ($futures as $item)
                @include('landing-page.features.item')
            @endforeach
        </div>
    </div>
</section>
