@php
	$disable_actions = $app_is_demo && (isset($category) && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'));
@endphp

<x-card
	class="chats-list-container flex h-[inherit] w-full shrink-0 grow-0 flex-col overflow-hidden rounded-e-none border-e-0 max-md:absolute max-md:start-0 max-md:top-0 max-md:z-50 max-md:h-full max-md:overflow-hidden max-md:border-none max-md:bg-background/95 max-md:backdrop-blur-lg max-md:backdrop-saturate-150 max-md:transition-all max-md:duration-300 md:!flex"
	class:body="flex flex-col h-full"
	id="chats-list-container"
	size="none"
	::class="{ 'active': mobileSidebarShow }"
>
	<div class="chats-search h-20 border-b p-5 max-xl:p-2.5">
		<form
			class="chats-search-form relative"
			action="#"
		>
			<x-forms.input
				class="navbar-search-input peer rounded-full border-clay bg-clay ps-10"
				id="chat_search_word"
				data-category-id="{{ $category->id }}"
				type="search"
				onkeydown="return event.key != 'Enter';"
				placeholder="{{ __('Search') }}"
				aria-label="{{ __('Search in website') }}"
			/>
			<x-tabler-search class="size-5 pointer-events-none absolute start-3 top-1/2 -translate-y-1/2 opacity-80" />
		</form>
	</div>
	<div
		class="chats-list grow-0 overflow-hidden"
		id="chat_sidebar_container"
	>
		@if (view()->hasSection('chat_sidebar_list'))
			@yield('chat_sidebar_list')
		@else
			@include('panel.user.openai_chat.components.chat_sidebar_list')
		@endif
	</div>
	<div class="chats-new mt-auto flex gap-2 px-6 py-8 max-md:hidden">
		@if (view()->hasSection('chat_sidebar_actions'))
			@yield('chat_sidebar_actions')
		@else
			@if (isset($category) && $category->slug == 'ai_pdf')
				<input
					id="selectDocInput"
					type="file"
					style="display: none;"
					accept=".pdf, .csv, .docx, .xlsx, .xls"
				/>
				<x-button
					class="lqd-upload-doc-trigger grow text-[0.8rem]"
					href="javascript:void(0);"
					onclick="return $('#selectDocInput').click();"
				>
					<x-tabler-plus class="size-4" />
					{{ __('Upload Document') }}
				</x-button>
			@else
				<x-button
					class="lqd-new-chat-trigger grow text-[0.8rem]"
					href="javascript:void(0);"
					onclick="{!! $disable_actions
                        ? 'return toastr.info(\'{{ __('This feature is disabled in Demo version.') }}\')'
                        : 'return startNewChat(\'{{ $category->id }}\', \'{{ LaravelLocalization::getCurrentLocale() }}\')' !!}"
				>
					<x-tabler-plus class="size-4" />
					{{ __('New Conversation') }}
				</x-button>
			@endif
		@endif
	</div>
</x-card>
