@php
    $domain = request()->getHost();
@endphp

<article
    class="theme-dark relative mx-4 rounded-xl bg-[#0A131F] text-base text-white/50 shadow-2xl shadow-black/10 md:mx-8"
    id="premium-support"
    style="background-image: url({{ custom_theme_url('/assets/img/bg/grid-bg.svg') }})"
>
    @include('premium-support.components.header')
    @include('premium-support.components.hero')
    @include('premium-support.components.feature-1')
    @include('premium-support.components.feature-2')
    @include('premium-support.components.feature-3')
    @include('premium-support.components.faq')
    @include('premium-support.components.cta')
    @include('premium-support.components.footer')
</article>
