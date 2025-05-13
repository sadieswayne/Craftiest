<div
    class="lqd-chat-head sticky -top-px z-30 flex min-h-20 items-center justify-between gap-2 rounded-se-[inherit] border-b bg-background/80 px-5 py-3 backdrop-blur-lg backdrop-saturate-150 max-md:bg-background/95 max-md:px-4">
    <div class="flex flex-col items-start justify-center text-sm">
        <x-dropdown.dropdown
            class="lqd-chat-category-dropdown static"
            class:dropdown-dropdown="end-2 start-2 max-h-[calc(100vh-270px)] overflow-y-auto overscroll-contain rounded-b-xl rounded-t-none shadow-[0_4px_20px_rgba(0,0,0,0.07)] sm:max-h-[calc(var(--chats-container-height,500px)-30px)] lg:end-4 lg:start-4"
            triggerType="click"
        >
            <x-slot:trigger
                class="gap-0.5 before:content-none hover:no-underline lg:gap-4"
            >
                <span
                    class="inline-flex size-11 items-center justify-center overflow-hidden overflow-ellipsis whitespace-nowrap rounded-full text-2xs font-medium text-foreground/65 transition-all group-hover:-translate-y-0.5"
                    style="background: {{ $category->color }};"
                >
                    @if ($category->slug === 'ai-chat-bot')
                        <img
                            class="lqd-chat-avatar-img size-full object-cover object-center"
                            src="{{ custom_theme_url('/assets/img/chat-default.jpg') }}"
                            alt="{{ __($category->name) }}"
                        >
                    @elseif ($category->image)
                        <img
                            class="lqd-chat-avatar-img size-full object-cover object-center"
                            src="{{ custom_theme_url($category->image, true) }}"
                            alt="{{ __($category->name) }}"
                        >
                    @else
                        <span class="block w-full overflow-hidden overflow-ellipsis whitespace-nowrap text-center">
                            {{ __($category->short_name) }}
                        </span>
                    @endif
                </span>
                <span class="m-0 flex flex-col gap-1 text-xs">
                    <span class="flex items-center justify-center gap-1 rounded-full bg-heading-foreground/5 px-2 py-1 font-semibold leading-tight max-sm:size-6 max-sm:p-0">
                        <span class="max-sm:hidden">
                            {{ $category->name }}
                        </span>
                        <x-tabler-chevron-down class="size-4 transition-transform group-[&.lqd-is-active]/dropdown:rotate-180" />
                    </span>
                    @if ($category->role != '')
                        <span class="m-0 block text-2xs text-heading-foreground/60 max-sm:hidden">
                            {{ __($category->role) }}
                        </span>
                    @endif
                </span>
            </x-slot:trigger>
            <x-slot:dropdown>
                <div
                    class="flex flex-col gap-3 px-4 py-4 sm:px-7"
                    x-data="{ searchString: '' }"
                    x-trap="open"
                >
                    <x-forms.input
                        class="rounded-full border-clay bg-clay ps-10"
                        container-class="mb-2"
                        type="search"
                        placeholder="{{ __('Search for chatbots') }}"
                        x-model="searchString"
                    >
                        <x-tabler-search class="absolute start-3 top-1/2 size-5 -translate-y-1/2" />
                        <svg
                            class="absolute end-3 top-1/2 -translate-y-1/2"
                            width="15"
                            height="11"
                            viewBox="0 0 15 11"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M5.83333 10.5V8.83333H9.16667V10.5H5.83333ZM2.5 6.33333V4.66667H12.5V6.33333H2.5ZM0 2.16667V0.5H15V2.16667H0Z" />
                        </svg>
                    </x-forms.input>
                    @foreach ($generators ?? [] as $generator)
                        <div
                            class="relative flex items-center gap-3 rounded-xl border px-5 py-3 transition-all hover:scale-[1.02] hover:shadow-lg hover:shadow-black/5"
                            x-show="searchString === '' || '{{ $generator->name }}'.toLowerCase().includes(searchString.toLowerCase()) || '{{ $generator->description }}'.toLowerCase().includes(searchString.toLowerCase())"
                        >
                            <!-- Icon -->
                            <div
                                class="lqd-chat-item-avatar inline-flex size-11 shrink-0 items-center justify-center overflow-hidden overflow-ellipsis whitespace-nowrap rounded-full border border-solid border-white/90 text-lg font-semibold text-black/65 shadow-[0_1px_2px_rgba(0,0,0,0.07)] transition-shadow group-hover:shadow-xl dark:border-current"
                                style="background: {{ $generator->color }};"
                            >
                                @if ($generator->slug === 'ai-chat-bot')
                                    <img
                                        class="lqd-chat-avatar-img size-full rounded-full object-cover object-center"
                                        src="{{ custom_theme_url('/assets/img/chat-default.jpg') }}"
                                        alt="{{ __($generator->name) }}"
                                    >
                                @elseif ($generator->image)
                                    <img
                                        class="lqd-chat-avatar-img size-full rounded-full object-cover object-center"
                                        src="{{ custom_theme_url($generator->image, true) }}"
                                        alt="{{ __($generator->name) }}"
                                    >
                                @else
                                    <span class="block w-full overflow-hidden overflow-ellipsis whitespace-nowrap text-center">
                                        {{ __($generator->short_name) }}
                                    </span>
                                @endif
                            </div>

                            <div>
                                <h4 class="m-0">{{ $generator->name }}</h4>
                                <p class="m-0 text-2xs">{{ $generator->description }}</p>
                            </div>

                            @if ($generator->plan === 'premium')
                                <span class="ms-auto inline-flex items-center gap-1 rounded-md bg-secondary p-2 text-3xs font-medium leading-tight text-secondary-foreground">
                                    {{-- blade-formatter-disable --}}
                                    <svg width="16" height="13" viewBox="0 0 19 15" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg"><path d="M7.75 7.5002L6 5.5752L6.525 4.7002M4.25 1.375H14.75L17.375 5.75L9.9375 14.0625C9.88047 14.1207 9.8124 14.1669 9.73728 14.1985C9.66215 14.2301 9.58149 14.2463 9.5 14.2463C9.41851 14.2463 9.33785 14.2301 9.26272 14.1985C9.1876 14.1669 9.11953 14.1207 9.0625 14.0625L1.625 5.75L4.25 1.375Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{-- blade-formatter-enable --}}
                                    {{ __('Premium') }}
                                </span>
                            @endif

                            <a
                                class="absolute inset-0"
                                href="{{ route('dashboard.user.openai.chat.chat', $generator->slug) }}"
                            ></a>
                        </div>
                    @endforeach
                </div>
            </x-slot:dropdown>
        </x-dropdown.dropdown>
    </div>

    <div class="flex grow items-center justify-end gap-4">
        <div class="flex gap-2">
            @includeFirst(['chat-share::share-button-include', 'panel.user.openai_chat.includes.share-button-include', 'vendor.empty'])
            @if (view()->hasSection('chat_head_actions'))
                @yield('chat_head_actions')
            @else
                @php
                    $realtimeHiddenIn = ['ai_pdf', 'ai_vision', 'ai_chat_image'];
                @endphp
                <x-forms.input
                    class="max-md:hidden"
                    id="realtime"
                    container-class="{{ in_array($category->slug, $realtimeHiddenIn, true) ? 'hidden' : 'flex' }} max-md:size-8 max-md:inline-flex max-md:items-center max-md:justify-center max-md:overflow-hidden max-md:shadow-md max-md:rounded-full max-md:shrink-0 max-md:[&_.lqd-input-label-txt]:hidden"
                    label="{{ __('Real-Time Data') }}"
                    type="checkbox"
                    name="realtime"
                    onchange="const checked = document.querySelector('#realtime').checked; if ( checked ) { toastr.success('Real-Time data activated') } else { toastr.warning('Real-Time data deactivated') }"
                    switcher
                >
                    <span
                        class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-background indent-0 text-heading-foreground transition-colors peer-checked:bg-primary peer-checked:text-primary-foreground md:hidden"
                    >
                        <x-tabler-world-download
                            class="size-5"
                            stroke-width="1.5"
                        />
                    </span>
                </x-forms.input>
            @endif

            <div
                class="group relative inline-flex flex-row items-center justify-center self-center max-md:-order-1"
                id="show_export_btns"
            >
                <button class="max-md:inline-flex max-md:size-8 max-md:items-center max-md:justify-center max-md:rounded-full max-md:shadow-md">
                    <x-tabler-clipboard-copy
                        class="size-6"
                        stroke-width="1.5"
                    />
                </button>
                <div
                    class="invisible absolute -end-4 bottom-full flex translate-y-2 scale-95 flex-row items-center justify-center rounded-lg bg-primary text-primary-foreground opacity-0 transition-all group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:scale-100 group-focus-within:opacity-100 group-hover:visible group-hover:translate-y-0 group-hover:scale-100 group-hover:opacity-100"
                    id="export_btns"
                >
                    <button
                        class="chat-download border-none px-3 py-1 text-3xs font-medium"
                        id="export_pdf"
                        data-doc-type="pdf"
                    >
                        {{ __('PDF') }}
                    </button>
                    <button
                        class="chat-download border-x border-x-primary-foreground/20 px-2.5 py-1 text-3xs font-medium"
                        id="export_word"
                        data-doc-type="doc"
                    >
                        {{ __('Word') }}
                    </button>
                    <button
                        class="chat-download px-3 py-1 text-3xs font-medium"
                        id="export_txt"
                        data-doc-type="txt"
                    >
                        {{ __('Txt') }}
                    </button>
                </div>
            </div>

            @if (view()->hasSection('chat_sidebar_actions'))
                @yield('chat_sidebar_actions')
            @else
                <x-button
                    class="lqd-upload-doc-trigger group size-8 shrink-0 grid-flow-row place-items-center rounded-full shadow-md max-md:grid md:hidden"
                    variant="none"
                    size="none"
                    href="javascript:void(0);"
                    onclick="{!! $disable_actions
                        ? 'return toastr.info(\'{{ __('This feature is disabled in Demo version.') }}\')'
                        : 'return deleteAllConv(\'{{ isset($category) ? $category->id : 0 }}\')' !!}"
                >
                    <x-tabler-trash class="size-5" />
                    <span class="sr-only">
                        {{ __('Clear All') }}
                    </span>
                </x-button>
                @if (isset($category) && $category->slug == 'ai_pdf')
                    {{-- #selectDocInput is present in chat_sidebar component. no need to duplicate it here --}}
                    <x-button
                        class="lqd-upload-doc-trigger group size-8 shrink-0 grid-flow-row place-items-center rounded-full shadow-md max-md:grid md:hidden"
                        variant="none"
                        size="none"
                        href="javascript:void(0);"
                        onclick="return $('#selectDocInput').click();"
                    >
                        <x-tabler-plus class="size-5" />
                        <span class="sr-only">
                            {{ __('Upload Document') }}
                        </span>
                    </x-button>
                @else
                    <x-button
                        class="lqd-new-chat-trigger group size-8 shrink-0 grid-flow-row place-items-center rounded-full shadow-md max-md:grid md:hidden"
                        variant="none"
                        size="none"
                        href="javascript:void(0);"
                        onclick="{!! $disable_actions
                            ? 'return toastr.info(\'{{ __('This feature is disabled in Demo version.') }}\')'
                            : 'return startNewChat(\'{{ $category->id }}\', \'{{ LaravelLocalization::getCurrentLocale() }}\')' !!}"
                    >
                        <x-tabler-plus class="size-5" />
                        <span class="sr-only">
                            {{ __('New Conversation') }}
                        </span>
                    </x-button>
                @endif

                <div class="lqd-chat-mobile-sidebar-trigger self-center">
                    <button
                        class="group size-8 shrink-0 grid-flow-row place-items-center rounded-full shadow-md max-md:grid md:hidden"
                        :class="{ 'active': mobileSidebarShow }"
                        @click.prevent="toggleMobileSidebar()"
                        type="button"
                    >
                        <x-tabler-dots class="col-start-1 row-start-1 size-5 transition-all group-[&.active]:rotate-45 group-[&.active]:scale-75 group-[&.active]:opacity-0" />
                        <x-tabler-x class="col-start-1 row-start-1 size-4 -rotate-45 opacity-0 transition-all group-[&.active]:rotate-0 group-[&.active]:!opacity-100" />
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
