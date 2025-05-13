<section class="site-section transition-all duration-700 md:translate-y-8 md:opacity-0 [&.lqd-is-in-view]:translate-y-0 [&.lqd-is-in-view]:opacity-100">
    <div class="container">
        <div class="rounded-[50px] border p-20 max-xl:px-10 max-lg:py-12 max-sm:px-5">
            <div
                class="lqd-tabs"
                data-lqd-tabs-style="1"
            >
                <div class="lqd-tabs-triggers mb-9 grid grid-cols-5 justify-between gap-8 max-lg:grid-cols-3 max-lg:gap-4 max-md:grid-cols-2 max-sm:grid-cols-1">
                    @foreach ($generatorsList as $item)
                        @include('landing-page.generators.item-trigger')
                    @endforeach
                </div>
                <div class="lqd-tabs-content-wrap">
                    @foreach ($generatorsList as $item)
                        @include('landing-page.generators.item-content')
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</section>
