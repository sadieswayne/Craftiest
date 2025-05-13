@extends('panel.layout.app', ['layout_wide' => true, 'disable_tblr' => true])
@section('title', __('AI Chat'))
@section('titlebar_actions')
	@php
		$route = 'dashboard.user.chat-setting.chat-template.create';
		$customChat = \Illuminate\Support\Facades\Route::has($route) && (setting('chat_setting_for_customer', 1) == 1);
	@endphp
	@if($customChat)
		<x-button href="{{  (route($route)) }}">
			<x-tabler-plus class="size-4" />
			{{ __('New') }}
		</x-button>
	@endif
@endsection
@section('titlebar_after')
    <div class="flex flex-col gap-4 md:flex-row md:items-center">
        <span class="relative">
            <x-tabler-search class="pointer-events-none absolute start-3 top-1/2 z-1 size-5 -translate-y-1/2" />
            <x-forms.input
                class="rounded-full border-none bg-clay ps-10"
                type="search"
                placeholder="{{ __('Search') }}"
                aria-label="{{ __('Search in website') }}"
                size="sm"
                x-data="{}"
                @keyup="$store.chatsFilter.setSearchStr($el.value)"
            />
        </span>

        <ul
            class="lqd-filter-list flex scroll-mt-6 flex-wrap items-center gap-x-4 gap-y-2 text-heading-foreground max-sm:gap-3"
            id="lqd-chats-filter-list"
        >
            <li>
                <x-button
                    class="lqd-filter-btn inline-flex rounded-full px-2.5 py-0.5 text-2xs leading-tight transition-colors hover:translate-y-0 hover:bg-foreground/5 [&.active]:bg-foreground/5"
                    tag="button"
                    type="button"
                    name="filter"
                    variant="ghost"
                    x-data="{}"
                    ::class="$store.chatsFilter.filter === 'all' && 'active'"
                    @click="$store.chatsFilter.changeFilter('all')"
                >
                    {{ __('All') }}
                </x-button>
            </li>
            <li>
                <x-button
                    class="lqd-filter-btn inline-flex rounded-full px-2.5 py-0.5 text-2xs leading-tight transition-colors hover:translate-y-0 hover:bg-foreground/5 [&.active]:bg-foreground/5"
                    tag="button"
                    type="button"
                    name="filter"
                    variant="ghost"
                    x-data="{}"
                    ::class="$store.chatsFilter.filter === 'favorite' && 'active'"
                    @click="$store.chatsFilter.changeFilter('favorite')"
                >
                    {{ __('Favorite') }}
                </x-button>
            </li>

            @foreach ($categoryList as $category)
                <li>
                    <x-button
                        class="lqd-filter-btn inline-flex rounded-full px-2.5 py-0.5 text-2xs leading-tight transition-colors hover:translate-y-0 hover:bg-foreground/5 [&.active]:bg-foreground/5"
                        tag="button"
                        type="button"
                        name="filter"
                        variant="ghost"
                        x-data="{}"
                        ::class="$store.generatorsFilter.filter === '{{ $category->name }}' && 'active'"
                        @click="$store.chatsFilter.changeFilter('{{ $category->name }}')"
                    >
                        {{ __(str()->ucfirst($category->name)) }}
                    </x-button>
                </li>
            @endforeach
        </ul>
    </div>
@endsection

@section('content')
    <div>
        @include('panel.user.openai_chat.components.list')
    </div>
@endsection

@push('script')
    <script>
        let message = @json($message);
        if (message === true) {
            toastr.warning("{{ __('Cannot access premium plan') }}");
        }
        let stream_type = '{!! $settings_two->openai_default_stream_server !!}';
        @if (setting('default_ai_engine', 'openai') == 'anthropic')
            const stream_type = 'backend';
        @endif
    </script>
    <script src="{{ custom_theme_url('/assets/js/panel/openai_chat.js') }}"></script>
@endpush
