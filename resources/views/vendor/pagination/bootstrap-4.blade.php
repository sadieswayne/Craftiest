@if ($paginator->hasPages())
    <nav>
        <ul class="pagination flex items-center gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li
                    class="page-item disabled"
                    aria-disabled="true"
                    aria-label="@lang('pagination.previous')"
                >
                    <span
                        class="page-link size-7 inline-flex items-center justify-center rounded-full text-xl transition-all"
                        aria-hidden="true"
                    >
                        &lsaquo;
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a
                        class="page-link size-7 inline-flex items-center justify-center rounded-full text-xl transition-all hover:-translate-x-0.5 hover:bg-primary/10"
                        href="{{ $paginator->previousPageUrl() }}"
                        rel="prev"
                        aria-label="@lang('pagination.previous')"
                    >
                        &lsaquo;
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li
                        class="page-item disabled"
                        aria-disabled="true"
                    >
                        <span class="page-link size-7 inline-flex items-center justify-center rounded-full transition-all hover:-translate-y-0.5 hover:bg-primary/10">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li
                                class="page-item active"
                                aria-current="page"
                            >
                                <span class="page-link size-7 inline-flex items-center justify-center rounded-full bg-primary text-primary-foreground transition-all">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a
                                    class="page-link size-7 inline-flex items-center justify-center rounded-full transition-all hover:-translate-y-0.5 hover:bg-primary/10"
                                    href="{{ $url }}"
                                >
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a
                        class="page-link size-7 inline-flex items-center justify-center rounded-full text-xl transition-all hover:translate-x-0.5 hover:bg-primary/10"
                        href="{{ $paginator->nextPageUrl() }}"
                        rel="next"
                        aria-label="@lang('pagination.next')"
                    >
                        &rsaquo;
                    </a>
                </li>
            @else
                <li
                    class="page-item disabled"
                    aria-disabled="true"
                    aria-label="@lang('pagination.next')"
                >
                    <span
                        class="page-link size-7 inline-flex items-center justify-center rounded-full text-xl transition-all"
                        aria-hidden="true"
                    >
                        &rsaquo;
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
