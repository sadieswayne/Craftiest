@php
    $filters = ['All', 'Installed', 'Free', 'Paid'];
@endphp

<div class="flex flex-col gap-4">
    <x-forms.input
        class="lqd-marketplace-search-input rounded-full bg-foreground/10 ps-10 placeholder:text-foreground"
        id="search_str"
        size="lg"
        type="search"
        placeholder="{{ __('Search for add-ons') }}"
    >
        <x-slot:icon>
            <span class="absolute start-3 top-1/2 -translate-y-1/2">
                <x-tabler-search class="size-5" />
            </span>
        </x-slot:icon>
    </x-forms.input>
    <ul class="lqd-filter-list m-0 mt-2 flex scroll-mt-6 list-none flex-wrap items-center gap-x-4 gap-y-2 border-b border-t p-0 py-1 text-heading-foreground max-sm:gap-3">
        @foreach ($filters as $filter)
            <li>
                <x-button
                    data-filter="{{ $filter }}"
                    @class([
                        'lqd-filter-btn addons_filter inline-flex rounded-full px-2.5 py-0.5 text-2xs leading-tight transition-colors hover:translate-y-0 hover:bg-foreground/5 [&.active]:bg-foreground/5',
                        'active' => $loop->first,
                    ])
                    tag="button"
                    type="button"
                    name="filter"
                    variant="ghost"
                >
                    {{ __($filter) }}
                </x-button>
            </li>
        @endforeach
    </ul>
</div>
