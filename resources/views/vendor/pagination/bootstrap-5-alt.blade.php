@if ($paginator->hasPages())
    <nav class="flex items-center">
        <div class="w-full sm:hidden">
            <ul class="pagination flex justify-between m-0 whitespace-nowrap">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="flex items-center page-item disabled" aria-disabled="true">
                        <svg class="rtl:-scale-x-100" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M15 6l-6 6l6 6"></path></svg>
                        <span class="page-link rounded-md">prev</span>
                    </li>
                @else
                    <li class="page-item flex items-center">
                        <svg class="rtl:-scale-x-100" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M15 6l-6 6l6 6"></path></svg>
                        <a class="page-link rounded-md" href="{{ $paginator->previousPageUrl() }}" rel="prev">{{__('Prev')}}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item flex items-center">
                        <a class="page-link rounded-md" href="{{ $paginator->nextPageUrl() }}" rel="next">{{__('Next')}}</a>
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M9 6l6 6l-6 6"></path> </svg>
                    </li>
                @else
                    <li class="flex items-center page-item disabled" aria-disabled="true">
						<span class="page-link rounded-md">{{__('Next')}}</span>
                        <svg class="rtl:-scale-x-100" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 6l6 6l-6 6"></path></svg>
                    </li>
                @endif
            </ul>
        </div>

        <div class="flex justify-between w-full">
            <div>
                <p class="m-0 text-muted">
                    {!! __('Showing') !!}
                    <span>{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span>{{ $paginator->lastItem() }}</span>
                    {!! __('of') !!}
                    <span>{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <ul class="pagination m-0 gap-1 flex items-center space-x-2">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="{{__('Prev')}}">
                            <span class="page-link" aria-hidden="true">
								<svg class="rtl:-scale-x-100" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M15 6l-6 6l6 6"></path></svg>
							</span>
                        </li>
                    @else
                        <li class="page-item hover:text-[#330582]">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{__('Prev')}}">
								<svg class="rtl:-scale-x-100" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M15 6l-6 6l6 6"></path></svg>
							</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active bg-[#330582] text-white rounded-full w-[24px] text-center" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item hover:text-[#330582]"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item hover:text-[#330582]">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{__('Next')}}">
								<svg class="rtl:-scale-x-100" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 6l6 6l-6 6"></path></svg>
							</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="{{__('Next')}}">
                            <span class="page-link" aria-hidden="true">
								<svg class="rtl:-scale-x-100" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 6l6 6l-6 6"></path></svg>
							</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif
