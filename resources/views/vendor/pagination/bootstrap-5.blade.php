@if ($paginator->hasPages())
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
                    <a
                        class="lqd-pagination-link flex h-full w-full items-center justify-center rounded-full transition-all hover:-translate-x-0.5 hover:bg-primary/10"
                        href="{{ $paginator->previousPageUrl() }}"
                    >
                @endif
                <x-tabler-chevron-left
                    class="size-5"
                    aria-hidden="true"
                />
                @if (!$paginator->onFirstPage())
                    </a>
                @endif
            </li>

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li
                        class="lqd-pagination-item size-7 pointer-events-none flex items-center justify-center rounded-full opacity-50"
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
                        <li @class([
                            'lqd-pagination-item flex size-7 items-center justify-center rounded-full transition-colors',
                            'active bg-primary text-primary-foreground' =>
                                $page == $paginator->currentPage(),
                            'hover:bg-primary/10' => $page != $paginator->currentPage(),
                        ])>
                            @if ($page != $paginator->currentPage())
                                <a
                                    class="lqd-pagination-link flex h-full w-full items-center justify-center rounded-full"
                                    href="{{ $url }}"
                                >
                            @endif
                            {{ $page }}
                            @if ($page != $paginator->currentPage())
                                </a>
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
                    <a
                        class="lqd-pagination-link flex h-full w-full items-center justify-center rounded-full transition-all hover:translate-x-0.5 hover:bg-primary/10"
                        href="{{ $paginator->nextPageUrl() }}"
                    >
                @endif
                <x-tabler-chevron-right
                    class="size-5"
                    aria-hidden="true"
                />
                @if ($paginator->hasMorePages())
                    </a>
                @endif
            </li>
        </ul>
    </nav>
@endif
