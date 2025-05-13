<div
    class="site-preheader flex h-12 items-center justify-center bg-[#343C57] bg-cover bg-center px-3 text-center text-[12px] text-white opacity-0 bg-blend-luminosity transition-all duration-500 group-[.page-loaded]/body:opacity-100"
    style="background-image: url({{ custom_theme_url('assets/img/landing-page/preheader-bg.jpg') }});"
>
    <p>
        <span class="mr-3 text-4xs font-semibold uppercase tracking-wide">
            {{ __($fSetting->header_title) }}
        </span>
        <span class="opacity-70">
            {{ __($fSetting->header_text) }}
        </span>
    </p>
</div>
