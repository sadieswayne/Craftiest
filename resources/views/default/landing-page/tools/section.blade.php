{!! adsense_tools_728x90() !!}
<section class="site-section py-10 transition-all duration-700 md:translate-y-8 md:opacity-0 [&.lqd-is-in-view]:translate-y-0 [&.lqd-is-in-view]:opacity-100">
    <div class="container">
        <div class="rounded-[50px] border p-10 max-sm:px-6 max-sm:py-16">
            <x-section-header
                mb="14"
                title="{{ __($fSectSettings->tools_title) }}"
                subtitle="{{ __($fSectSettings->tools_description) ?? __('MagicAI has all the tools you need to create and manage your SaaS platform.') }}"
            />
            <div class="grid grid-cols-3 gap-3 max-lg:grid-cols-2 max-md:grid-cols-1">
                @foreach ($tools as $item)
                    @include('landing-page.tools.item')
                @endforeach
            </div>
        </div>
    </div>
</section>
