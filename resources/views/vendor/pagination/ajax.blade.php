@php
    if (!function_exists('get_page_number')) {
        function get_page_number(string $page_url)
        {
            $data = parse_url($page_url);
            parse_str($data['query'], $output);

            return $output['page'] ?? '1';
        }
    }

    if (!isset($target_id)) {
        $target_id = 'lqd-posts-container';
    }

    if (!isset($currfolder)) {
        $currfolder = null;
    }
@endphp

@if ($paginator->hasPages())
    <form
        action="{{ $action }}"
        method="GET"
        x-init
        x-target="{{ $target_id }}"
    >
        <input
            type="hidden"
            name="filter"
            :value="$store.documentsFilter.filter"
        >
        <input
            type="hidden"
            name="sort"
            :value="$store.documentsFilter.sort"
        >
        <input
            type="hidden"
            name="sortAscDesc"
            :value="$store.documentsFilter.sortAscDesc"
        >
        <nav class="lqd-pagination flex items-center justify-between pb-6 pt-10">
            <p class="m-0 text-foreground/70">
                {{ __('Showing') }}
                <span>{{ $paginator->firstItem() }}</span>
                {{ __('to') }}
                <span>{{ $paginator->lastItem() }}</span>
                {{ __('of') }}
                <span>{{ $paginator->total() }}</span>
                {{ __('results') }}
            </p>

            <ul class="lqd-pagination-list flex items-center gap-1">
                {{-- Previous Page Link --}}
                <li
                    @class([
                        'lqd-pagination-item flex w-7 h-7 items-center justify-center rounded-full',
                        'opacity-50 pointer-events-none' => $paginator->onFirstPage(),
                    ])
                    aria-label="{{ __('Prev') }}"
                    @disabled($paginator->onFirstPage())
                >
                    @if (!$paginator->onFirstPage())
                        <button
                            class="lqd-pagination-link flex h-full w-full items-center justify-center rounded-full transition-all hover:-translate-x-0.5 hover:bg-primary/10"
                            type="submit"
                            value="{{ get_page_number($paginator->previousPageUrl()) }}"
                            name="page"
                            @click="$store.documentsFilter.changePage( '<' )"
                        >
                    @endif
                    <x-tabler-chevron-left
                        class="size-5"
                        aria-hidden="true"
                    />
                    @if (!$paginator->onFirstPage())
                        </button>
                    @endif
                </li>

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li
                            class="lqd-pagination-item pointer-events-none flex size-7 items-center justify-center rounded-full opacity-50"
                            aria-disabled="true"
                            aria-hidden="true"
                        >
                            <span class="lqd-pagination-link flex h-full w-full items-center justify-center">
                                {{ $element }}
                            </span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <li
                                @class([
                                    'lqd-pagination-item flex size-7 items-center justify-center rounded-full transition-colors [&.active]:bg-primary [&.active]:text-primary-foreground',
                                    'active' => $page == $paginator->currentPage(),
                                    'hover:bg-primary/10' => $page != $paginator->currentPage(),
                                ])
                                :class="$store.documentsFilter.page == '{{ get_page_number($url) }}'"
                            >
                                @if ($page != $paginator->currentPage())
                                    <button
                                        class="lqd-pagination-link flex h-full w-full items-center justify-center rounded-full"
                                        type="submit"
                                        value="{{ get_page_number($url) }}"
                                        name="page"
                                        @click="$store.documentsFilter.changePage( {{ get_page_number($url) }} )"
                                    >
                                @endif
                                {{ $page }}
                                @if ($page != $paginator->currentPage())
                                    </button>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                <li
                    @class([
                        'lqd-pagination-item flex w-7 h-7 items-center justify-center rounded-full',
                        'opacity-50 pointer-events-none' => !$paginator->hasMorePages(),
                    ])
                    aria-label="{{ __('Next') }}"
                    @disabled(!$paginator->hasMorePages())
                >
                    @if ($paginator->hasMorePages())
                        <button
                            class="lqd-pagination-link flex h-full w-full items-center justify-center rounded-full transition-all hover:translate-x-0.5 hover:bg-primary/10"
                            type="submit"
                            value="{{ get_page_number($paginator->nextPageUrl()) }}"
                            name="page"
                            @click="$store.documentsFilter.changePage( '>' )"
                        >
                    @endif
                    <x-tabler-chevron-right
                        class="size-5"
                        aria-hidden="true"
                    />
                    @if ($paginator->hasMorePages())
                        </button>
                    @endif
                </li>
            </ul>
        </nav>
    </form>
@endif
