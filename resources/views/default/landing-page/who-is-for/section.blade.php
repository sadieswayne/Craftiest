<section class="site-section py-20 transition-all duration-700 md:translate-y-8 md:opacity-0 [&.lqd-is-in-view]:translate-y-0 [&.lqd-is-in-view]:opacity-100">
    <div class="container">
        <div class="grid grid-cols-3 gap-4 max-lg:grid-cols-2 max-md:grid-cols-1">
            @foreach ($who_is_for as $item)
                @include('landing-page.who-is-for.item', ['item' => $item])
            @endforeach
        </div>
    </div>
</section>
