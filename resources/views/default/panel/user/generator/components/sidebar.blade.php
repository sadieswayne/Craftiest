@php
    $plan = Auth::user()->activePlan();
    $plan_type = 'regular';

    if ($plan != null) {
        $plan_type = strtolower($plan->plan_type);
    }

    $auth = Auth::user();
@endphp

@push('style')
    <style>
        @keyframes sidebar-step-slide-in {
            from {
                transform: translateX(25px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes sidebar-step-slide-out {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(-25px);
                opacity: 0;
            }
        }

        ::view-transition-old(sidebar-step-container) {
            animation: sidebar-step-slide-out 0.3s ease both;
        }

        ::view-transition-new(sidebar-step-container) {
            animation: sidebar-step-slide-in 0.3s ease both;
        }

        /* Make the magic happen */
        .lqd-generator-sidebar-step-container {
            view-transition-name: sidebar-step-container;
        }
    </style>
@endpush

<div
    class="lqd-generator-sidebar-backdrop visible fixed inset-0 z-30 flex items-center justify-center bg-background/90 ps-[--sidebar-w] opacity-100 transition-opacity duration-300"
    x-init
    :class="{ 'opacity-100': !sideNavCollapsed, 'opacity-0': sideNavCollapsed, 'invisible': sideNavCollapsed }"
    @click="toggleSideNavCollapse('collapse')"
>
    <div class="flex max-w-sm cursor-pointer flex-col items-center justify-center gap-5 px-8 text-center max-md:hidden">
        <img
            class="mx-auto h-auto w-full max-w-[40px] group-[.navbar-shrinked]/body:block dark:hidden"
            src="{{ custom_theme_url($setting->logo_collapsed_path, true) }}"
            @if (isset($setting->logo_collapsed_2x_path)) srcset="/{{ $setting->logo_collapsed_2x_path }} 2x" @endif
            alt="{{ $setting->site_name }}"
        >
        <h4 class="m-0 text-[19px]">@lang('Your content will appear here.')</h4>
        <p class="m-0 text-[15px] font-medium text-heading-foreground/50">
            @lang('Simply select a pre-built template or create your own')
            <span class="text-heading-foreground underline">
                @lang('custom content here.')
            </span>
        </p>
    </div>
</div>

<div
    class="lqd-generator-sidebar group/sidebar fixed bottom-0 start-0 top-[--editor-tb-h] z-40 w-[--sidebar-w] translate-x-0 bg-background shadow-[2px_4px_26px_rgba(0,0,0,0.05)] transition-all duration-500 ease-[cubic-bezier(0.25,0.8,0.49,1.0)] group-[&.lqd-generator-sidebar-collapsed]/generator:-translate-x-[calc(100%-35px)] rtl:group-[&.lqd-generator-sidebar-collapsed]/generator:translate-x-[calc(100%-35px)]">
    <button
        class="lqd-generator-sidebar-toggle size-5 absolute -end-2.5 top-16 z-10 flex origin-center translate-x-0 items-center justify-center rounded-full bg-heading-foreground/15 p-0 text-heading-foreground/90 transition-colors hover:bg-heading-foreground hover:text-background max-lg:top-1/2 max-lg:-translate-y-1/2 max-md:!h-7 max-md:!w-7"
        @click.prevent="toggleSideNavCollapse()"
        :class="{ 'rotate-180': sideNavCollapsed }"
    >
        <span class="size-10 absolute start-1/2 top-1/2 inline-block -translate-x-1/2 -translate-y-1/2"></span>
        <x-tabler-chevron-left class="size-4 rtl:rotate-180" />
    </button>

    <div
        class="lqd-generator-sidebar-inner size-full translate-x-0 overflow-y-auto pt-5 transition-all delay-100 duration-300 ease-out group-[&.lqd-generator-sidebar-collapsed]/generator:-translate-x-4 group-[&.lqd-generator-sidebar-collapsed]/generator:opacity-0">
        <div class="lqd-steps mb-5 px-5">
            <p class="lqd-step flex items-center justify-between gap-4 text-sm font-semibold">
                <span class="flex items-center justify-between gap-3">
                    <span
                        class="size-5 inline-flex items-center justify-center rounded-md bg-primary text-[12px] font-medium text-primary-foreground"
                        x-text="generatorStep + 1"
                    >
                        1
                    </span>
                    <span
                        class="active hidden [&.active]:inline-block"
                        :class="{ 'active': generatorStep === 0 }"
                    >
                        {{ __('Choose a Template') }}
                    </span>
                    <span
                        class="hidden [&.active]:inline-block"
                        :class="{ 'active': generatorStep === 1 }"
                    >
                        {{ __('Add Information') }}
                    </span>
                </span>

                <button
                    class="size-6 hidden cursor-default items-center justify-center p-0 text-inherit [&.active]:flex"
                    {{-- :class="{ 'active': generatorStep === 0 }" --}}
                >
                    <svg
                        class="size-5"
                        xmlns="http://www.w3.org/2000/svg"
                        width="44"
                        height="44"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            stroke="none"
                            d="M0 0h24v24H0z"
                            fill="none"
                        />
                        <path d="M4 6l16 0" />
                        <path d="M8 12l8 0" />
                        <path d="M6 18l12 0" />
                    </svg>
                </button>

                <button
                    class="size-6 hidden items-center justify-center rounded-md p-0 text-inherit transition-all hover:bg-foreground/5 [&.active]:flex"
                    :class="{ 'active': generatorStep === 1 }"
                    @click.prevent="setGeneratorStep(0)"
                >
                    <x-tabler-grid-dots class="size-5" />
                </button>
            </p>
        </div> <!-- .lqd-steps -->

        <div
            class="lqd-generator-sidebar-step-container"
            data-step="0"
            :class="{ 'hidden': generatorStep !== 0 }"
        >
            <form
                class="lqd-generator-search-form group mb-6 px-5"
                @submit.prevent
            >
                <div class="relative w-full">
                    <x-tabler-search
                        class="lqd-header-search-icon pointer-events-none absolute start-3 top-1/2 z-10 w-5 -translate-y-1/2 opacity-75 max-lg:start-6"
                        stroke-width="1.5"
                    />
                    <x-forms.input
                        class="peer"
                        class="rounded-full border-clay bg-clay ps-10"
                        type="search"
                        @keyup="setItemsSearchStr($el.value)"
                        placeholder="{{ __('Search for templates and documents...') }}"
                        @keyup.slash.prevent.window="$el.focus()"
                        aria-label="Search in website"
                    />
                    <kbd
                        class="peer-focus-within:scale-70 pointer-events-none absolute end-3 top-1/2 z-10 inline-block -translate-y-1/2 rounded-full bg-background px-2 py-1 text-3xs leading-none transition-all peer-focus-within:invisible peer-focus-within:opacity-0 max-lg:hidden">
                        /
                    </kbd>
                    <span class="absolute end-5 top-1/2 -translate-y-1/2">
                        <x-tabler-loader-2
                            class="hidden animate-spin group-[.is-searching]:block"
                            stroke-width="1.5"
                            role="status"
                        />
                    </span>
                    <span
                        class="pointer-events-none absolute end-3 top-1/2 -translate-x-2 -translate-y-1/2 opacity-0 transition-all group-[.done-searching]:hidden group-[.is-searching]:hidden peer-focus-within:translate-x-0 peer-focus-within:opacity-100 rtl:-scale-x-100"
                    >
                        <x-tabler-chevron-right class="size-5" />
                    </span>
                </div>
            </form>

            <div
                class="lqd-generator-categories"
                x-data="{ activeFilter: '{{ $filters->first()->name }}' }"
            >
                @foreach ($filters as $filter)
                    @php
                        $cat_items = $list->filter(function ($list_item) use ($filter) {
                            return str()->contains($list_item->filters, $filter->name) && $list_item->active == 1 && !Str::startsWith($list_item->slug, 'ai_');
                        });
                    @endphp
                    @if (!$cat_items->isEmpty())
                        <div class="lqd-generator-category group/cat">
                            <button
                                class="lqd-generator-filter-trigger flex w-full items-center justify-between gap-2 border-t px-5 py-6 font-semibold leading-tight text-heading-foreground group-first/cat:border-t-0"
                                @click.prevent="activeFilter === '{{ $filter->name }}' ? activeFilter = '' : activeFilter = '{{ $filter->name }}'"
                            >
                                {{ str()->ucfirst($filter->name) }}
                                <x-tabler-chevron-up
                                    class="size-4 transition-transform group-[&.lqd-showing-search-results]/sidebar:!rotate-0"
                                    ::class="{ 'rotate-180': activeFilter !== '{{ $filter->name }}' }"
                                />
                            </button>

                            <div
                                @class([
                                    'lqd-generator-category-list flex flex-col gap-4 pt-3 pb-8 px-5 group-[&.lqd-showing-search-results]/sidebar:!flex',
                                    'hidden' => !$loop->first,
                                ])
                                :class="{ 'hidden': activeFilter !== '{{ $filter->name }}' }"
                            >
                                @foreach ($cat_items as $item)
                                    @if ($item->active != 1 || Str::startsWith($item->slug, 'ai_'))
                                        @continue
                                    @endif
                                    @php
                                        $upgrade = false;
                                        if ($app_is_demo) {
                                            if ($item->premium == 1 && $plan_type === 'regular') {
                                                $upgrade = true;
                                            }
                                        } else {
                                            if (!$auth->isAdmin() && $item->premium == 1 && $plan_type === 'regular') {
                                                $upgrade = true;
                                            }
                                        }
                                    @endphp

                                    <div
                                        class="lqd-generator-item group relative flex w-full items-center gap-2 rounded-full bg-[#E2E5FF] px-2.5 py-2 transition-all hover:scale-[1.03] hover:shadow-xl hover:shadow-black/5"
                                        data-title="{{ str(__($item->title))->lower()->value() }}"
                                        data-description="{{ str(__($item->description))->lower()->value() }}"
                                        data-filter="{{ $item->filters }}"
                                        :class="{
                                            'hidden': itemsSearchStr !== '' && (!$el.getAttribute('data-title')
                                                .includes(itemsSearchStr) && !$el.getAttribute(
                                                    'data-description').includes(itemsSearchStr) && !$el
                                                .getAttribute('data-filter').includes(itemsSearchStr)),
                                        }"
                                    >
                                        <span
                                            class="size-9 [&_svg]:size-5 relative flex items-center justify-center rounded-full bg-white shadow-sm transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg"
                                        >
                                            @if ($item->image !== 'none')
                                                <span class="inline-block transition-all duration-300 group-hover:scale-110">
                                                    {!! html_entity_decode($item->image) !!}
                                                </span>
                                            @endif
                                            <span @class([
                                                'absolute bottom-0 end-0 size-3 rounded border-2 border-solid border-white rounded-full',
                                                'bg-green-500' => $item->active == 1,
                                                'bg-red-500' => $item->active != 1,
                                            ])></span>
                                        </span>

                                        <div>
                                            <h4 class="relative m-0 inline-block text-sm font-medium dark:text-black">
                                                {{ __($item->title) }}
                                                <span
                                                    class="absolute start-[calc(100%+0.35rem)] top-1/2 inline-block -translate-x-1 -translate-y-1/2 align-bottom opacity-0 transition-all group-hover:translate-x-0 group-hover:!opacity-100 rtl:-scale-x-100"
                                                >
                                                    <x-tabler-chevron-right class="size-4" />
                                                </span>
                                            </h4>
                                        </div>

                                        @if ($item->active == 1)
                                            <div class="@if ($upgrade) bg-background opacity-75 @endif size-full absolute left-0 top-0 z-2 transition-all">
                                                @if ($upgrade)
                                                    <div class="absolute right-2 top-2 z-10 rounded-md bg-[#E2FFFC] px-2 py-0.5 font-medium text-black">
                                                        {{ __('Upgrade') }}
                                                    </div>
                                                    <a
                                                        class="size-full absolute left-0 top-0 inline-block overflow-hidden text-start -indent-[99999px]"
                                                        href="{{  (route('dashboard.user.payment.subscription')) }}"
                                                    >
                                                        {{ __('Upgrade') }}
                                                    </a>
                                                @elseif($item->type == 'text' or $item->type == 'code')
                                                    @if ($item->slug == 'ai_article_wizard_generator')
                                                        <a
                                                            class="size-full absolute left-0 top-0 inline-block overflow-hidden text-start -indent-[99999px]"
                                                            href="{{  (route('dashboard.user.openai.articlewizard.new')) }}"
                                                        >
                                                            {{ __('Create Workbook') }}
                                                        </a>
                                                    @else
                                                        <a
                                                            class="size-full absolute left-0 top-0 inline-block overflow-hidden text-start -indent-[99999px]"
                                                            href="{{  (route('dashboard.user.openai.generator.workbook', $item->slug)) }}"
                                                        >
                                                            {{ __('Create Workbook') }}
                                                        </a>
                                                    @endif
                                                @elseif($item->type == 'voiceover' || $item->type == 'isolator')
                                                    <a
                                                        class="size-full absolute left-0 top-0 inline-block overflow-hidden text-start -indent-[99999px]"
                                                        href="{{  (route('dashboard.user.openai.generator', $item->slug)) }}"
                                                    >
                                                        {{ __('Create Workbook') }}
                                                    </a>
                                                @elseif($item->type == 'image')
                                                    <a
                                                        class="size-full absolute left-0 top-0 inline-block overflow-hidden text-start -indent-[99999px]"
                                                        href="{{  (route('dashboard.user.openai.generator', $item->slug)) }}"
                                                    >
                                                        {{ __('Create') }}
                                                    </a>
                                                @elseif($item->type == 'audio')
                                                    <a
                                                        class="size-full absolute left-0 top-0 inline-block overflow-hidden text-start -indent-[99999px]"
                                                        href="{{  (route('dashboard.user.openai.generator', $item->slug)) }}"
                                                    >
                                                        {{ __('Create') }}
                                                    </a>
                                                @else
                                                    <div class="absolute inset-0 flex items-center justify-center bg-zinc-900/5 backdrop-blur-[1px]">
                                                        <a
                                                            class="btn text-dark pointer-events-none cursor-default bg-white"
                                                            href="#"
                                                            disabled=""
                                                        >
                                                            {{ __('No Tokens Left') }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Getting generator options via ajax call --}}
                                            <a
                                                class="absolute inset-0 z-20 inline-block [&.loading]:animate-pulse [&.loading]:bg-white/50"
                                                data-href="/dashboard/user/generator/option/{{ $item->slug }}"
                                                x-init="$el.setAttribute('href', $el.getAttribute('data-href'))"
                                                x-target="lqd-generator-options"
                                                @ajax:before="document.querySelector('#document_title').value = ''; $el.classList.add('loading')"
                                                @ajax:success="setGeneratorStep(1)"
                                                @ajax:after="$el.classList.remove('loading')"
                                                oncontextmenu="return false"
                                            ></a>
                                        @endif
                                    </div> <!-- .lqd-generator-item -->
                                @endforeach
                            </div> <!-- .lqd-generator-category-list -->
                        </div> <!-- .lqd-generator-category -->
                    @endif
                @endforeach
            </div> <!-- .lqd-generator-categories -->
        </div> <!-- .lqd-generator-sidebar-step-container -->

        <div
            class="lqd-generator-sidebar-step-container hidden"
            data-step="1"
            :class="{ 'hidden': generatorStep !== 1 }"
        >
            <div
                class="lqd-generator-options px-5 !pb-8"
                id="lqd-generator-options"
                x-merge.transition
            >

            </div> <!-- .lqd-generator-options -->

        </div> <!-- .lqd-generator-sidebar-step-container -->
    </div> <!-- .lqd-generator-sidebar-inner -->
</div> <!-- .lqd-generator-sidebar -->

<input
    id="guest_id"
    type="hidden"
    value="{{ $apiUrl }}"
>
<input
    id="guest_event_id"
    type="hidden"
    value="{{ $apikeyPart1 }}"
>
<input
    id="guest_look_id"
    type="hidden"
    value="{{ $apikeyPart2 }}"
>
<input
    id="guest_product_id"
    type="hidden"
    value="{{ $apikeyPart3 }}"
>
