{!! adsense_templates_728x90() !!}
<section
    class="site-section pb-9 transition-all duration-700 md:translate-y-8 md:opacity-0 [&.lqd-is-in-view]:translate-y-0 [&.lqd-is-in-view]:opacity-100"
    id="templates"
>
    <div class="container">
        <div class="rounded-[50px] border p-10 max-sm:px-5">
            <x-section-header
                mb="7"
                width="w-3/5"
                title="{!! __($fSectSettings->custom_templates_title) !!}"
                subtitle="{!! $fSectSettings->custom_templates_description ??
                    'Create your own template or use pre-made templates and examples for various content types and industries to help you get started quickly.' !!}"
            >
                <h6 class="mb-6 inline-block rounded-md bg-[#083D91] bg-opacity-15 px-3 py-1 text-[13px] font-medium text-[#083D91]">
                    {!! __($fSectSettings->custom_templates_subtitle_one) !!}
                    <span class="dot"></span>
                    <span class="opacity-50">{!! __($fSectSettings->custom_templates_subtitle_two) !!}</span>
                </h6>
            </x-section-header>
            <div class="flex flex-col items-center">
                <div class="mx-auto mb-10 inline-flex flex-wrap items-center gap-2 rounded-lg border p-[0.35rem] text-[12px] font-semibold leading-none max-md:justify-center">
                    <x-tabs-trigger
                        target=".templates-all"
                        style="2"
                        label="{{ __('All') }}"
                        active="true"
                    />
                    @foreach ($filters as $filter)
                        <x-tabs-trigger
                            target=".templates-{{ \Illuminate\Support\Str::slug($filter->name) }}"
                            style="2"
                            label="{{ __($filter->name) }}"
                        />
                    @endforeach
                </div>
            </div>
            <div class="relative">
                <div class="templates-cards grid max-h-[28rem] grid-cols-3 gap-4 overflow-hidden max-lg:grid-cols-2 max-md:grid-cols-1">
                    @foreach ($templates as $item)
                        @if ($item->active != 1)
                            @continue
                        @endif
                        @include('landing-page.custom-templates.item')
                    @endforeach
                </div>
                <div class="templates-cards-overlay absolute inset-x-0 bottom-0 z-10 h-[230px] bg-gradient-to-t from-background from-20% to-transparent">
                </div>
            </div>
            <div class="relative z-20 mt-2 text-center">
                <button class="templates-show-more text-[14px] font-semibold text-[#5A4791]">
                    <span class="size-7 mr-1 inline-grid place-content-center rounded-lg bg-[#885EFE] bg-opacity-10">
                        <svg
                            width="12"
                            height="12"
                            viewBox="0 0 12 12"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M5.671 11.796V0.996H7.125V11.796H5.671ZM0.998 7.125V5.671H11.798V7.125H0.998Z" />
                        </svg>
                    </span>
                    <span class="inline-grid h-7 place-content-center rounded-lg bg-[#885EFE] bg-opacity-10 px-2">
                        {{ __('Show more') }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</section>
