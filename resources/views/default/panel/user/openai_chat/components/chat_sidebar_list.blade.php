@php
    $disable_actions = $app_is_demo && (isset($category) && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'));
@endphp

<ul class="chat-list-ul flex h-full flex-col overflow-y-auto text-xs">
    @foreach ($list as $entry)
        <li
            id="chat_{{ $entry->id }}"
            @class([
                'chat-list-item shrink-0 group relative border-b overflow-hidden [word-break:break-word] [&.active]:before:absolute [&.active]:before:left-0 [&.active]:before:top-[25%] [&.active]:before:h-[50%] [&.active]:before:w-[3px] [&.active]:before:bg-gradient-to-b [&.active]:before:from-primary [&.active]:before:to-transparent',
                'pin-mode' => $entry->is_pinned,
                'active' => isset($chat) && $chat->id == $entry->id,
            ])
        >
            <div
                class="chat-list-item-trigger flex cursor-pointer gap-3 p-5 text-start text-heading-foreground hover:text-primary group-[&.edit-mode]:pointer-events-none dark:hover:text-heading-foreground"
                onclick="return openChatAreaContainer({{ $entry->id }});"
                @click="mobileSidebarShow = false"
            >
                <div class="flex flex-col gap-y-2">
                    <x-tabler-pinned
                        class="hidden size-6 group-[&.pin-mode]:block"
                        stroke-width="1.5"
                    />
                    <x-tabler-message
                        class="size-6 shrink-0 group-[&.pin-mode]:hidden"
                        stroke-width="1.5"
                    />
                </div>
                <span class="flex flex-col">
                    <span class="chat-item-title text-xs font-medium group-[&.edit-mode]:pointer-events-auto">
                        {{ __($entry->title) }}
                    </span>
                    <span class="chat-item-date text-3xs opacity-40">{{ $entry->updated_at->diffForHumans() }}</span>
                    @if ($entry->reference_url != '')
                        <a
                            class="flex underline opacity-90"
                            target="_blank"
                            title="{{ $entry->reference_url }}"
                            onclick="event.stopPropagation();"
                            href="{{ $entry->reference_url }}"
                        >
                            {{ __($entry->doc_name) }}
                        </a>
                    @endif
                    @if ($entry->website_url != '')
                        <a
                            class="flex underline opacity-90"
                            target="_blank"
                            title="{{ $entry->website_url }}"
                            onclick="event.stopPropagation();"
                            href="{{ $entry->website_url }}"
                        >
                            {{ __($entry->website_url) }}
                        </a>
                    @endif
                </span>
            </div>
            <span
                class="chat-list-item-actions absolute end-4 top-1/2 flex -translate-y-1/2 gap-1 opacity-0 transition-opacity before:pointer-events-none before:absolute before:-inset-9 before:z-0 before:bg-[radial-gradient(closest-side,hsl(var(--background))_50%,transparent)] before:opacity-0 before:transition-all focus-within:opacity-100 focus-within:before:opacity-100 group-hover:opacity-100 group-hover:before:opacity-90 group-[&.edit-mode]:opacity-100 max-md:opacity-100"
            >
                <button
                    @class([
                        'chat-item-pin' => !$disable_actions,
                        'flex size-7 items-center relative z-1 justify-center rounded-full border bg-background transition-all dark:bg-primary dark:text-primary-foreground dark:border-primary hover:scale-110  group-[&.edit-mode]:hidden',
                    ])
                    @if ($disable_actions) onclick="return toastr.info('{{ __('This feature is disabled in Demo version.') }}')" @endif
                >
                    <x-tabler-pin class="size-4 group-[&.pin-mode]:hidden" />
                    <x-tabler-pinned class="hidden size-4 group-[&.pin-mode]:block" />
                </button>
                <button
                    @class([
                        'chat-item-update-title' => !$disable_actions,
                        'flex size-7 items-center relative z-1 justify-center rounded-full border bg-background transition-all dark:bg-primary dark:text-primary-foreground dark:border-primary hover:scale-110 group-[&.edit-mode]:bg-emerald-500 group-[&.edit-mode]:border-emerald-500 group-[&.edit-mode]:text-white',
                    ])
                    @if ($disable_actions) onclick="return toastr.info('{{ __('This feature is disabled in Demo version.') }}')" @endif
                >
                    <x-tabler-pencil class="size-4 group-[&.edit-mode]:hidden" />
                    <x-tabler-check class="hidden size-4 group-[&.edit-mode]:block" />
                </button>
                <button
                    @class([
                        'chat-item-delete' => !$disable_actions,
                        'flex size-7 items-center relative z-1 justify-center rounded-full border border-red-600 bg-red-600 text-white transition-all hover:scale-110 group-[&.edit-mode]:hidden',
                    ])
                    @if ($disable_actions) onclick="return toastr.info('{{ __('This feature is disabled in Demo version.') }}')" @endif
                >
                    <x-tabler-x class="size-4" />
                </button>
            </span>
        </li>
    @endforeach
</ul>

<ul class="chat-list-ul h-full overflow-y-auto text-xs">
    @foreach ($list as $entry)
        <li
            id="chat_{{ $entry->id }}"
            @class([
                'chat-list-item ' .
                ($entry->is_pinned ? 'pin-mode' : '') .
                ' group relative border-b overflow-hidden [word-break:break-word] [&.active]:before:absolute [&.active]:before:left-0 [&.active]:before:top-[25%] [&.active]:before:h-[50%] [&.active]:before:w-[3px] [&.active]:before:bg-gradient-to-b [&.active]:before:from-primary [&.active]:before:to-transparent',
                'active' => isset($chat) && $chat->id == $entry->id,
            ])
        >
            <div
                class="chat-list-item-trigger flex cursor-pointer gap-3 p-5 text-start text-heading-foreground hover:text-primary group-[&.edit-mode]:pointer-events-none dark:hover:text-heading-foreground"
                onclick="return openChatAreaContainer({{ $entry->id }});"
                @click="mobileSidebarShow = false"
            >
                <x-tabler-message
                    class="size-6 shrink-0"
                    stroke-width="1.5"
                />
                <span class="flex flex-col">
                    <span class="chat-item-title text-xs font-medium group-[&.edit-mode]:pointer-events-auto">
                        {{ __($entry->title) }}
                    </span>
                    <span class="chat-item-date text-3xs opacity-40">{{ $entry->updated_at->diffForHumans() }}</span>
                    @if ($entry->reference_url != '')
                        <a
                            class="flex underline opacity-90"
                            target="_blank"
                            title="{{ $entry->reference_url }}"
                            onclick="event.stopPropagation();"
                            href="{{ $entry->reference_url }}"
                        >
                            {{ __($entry->doc_name) }}
                        </a>
                    @endif
                    @if ($entry->website_url != '')
                        <a
                            class="flex underline opacity-90"
                            target="_blank"
                            title="{{ $entry->website_url }}"
                            onclick="event.stopPropagation();"
                            href="{{ $entry->website_url }}"
                        >
                            {{ __($entry->website_url) }}
                        </a>
                    @endif
                </span>
            </div>
            <span
                class="chat-list-item-actions absolute end-4 top-1/2 flex -translate-y-1/2 gap-1 opacity-0 transition-opacity before:pointer-events-none before:absolute before:-inset-9 before:z-0 before:bg-[radial-gradient(closest-side,hsl(var(--background))_50%,transparent)] before:opacity-0 before:transition-all focus-within:opacity-100 group-hover:opacity-100 group-hover:before:opacity-90 group-[&.edit-mode]:opacity-100 max-md:opacity-100"
            >
                <!-- Pin Button -->
                <button
                    @class([
                        'chat-item-pin' => !$disable_actions,
                        'flex size-7 items-center relative z-1 justify-center rounded-full border bg-background transition-all dark:bg-primary dark:text-primary-foreground dark:border-primary hover:scale-110  group-[&.edit-mode]:hidden',
                    ])
                    @if ($disable_actions) onclick="return toastr.info({{ __('This feature is disabled in Demo version.') }})" @endif
                >
                    <!-- Show the pin icon if not pinned -->
                    <x-tabler-pin class="{{ $entry->is_pinned ? 'hidden' : '' }} size-4" />

                    <!-- Show the pinned icon if pinned -->
                    <x-tabler-pinned class="{{ $entry->is_pinned ? '' : 'hidden' }} size-4" />
                </button>

                <!-- Update Title Button -->
                <button
                    @class([
                        'chat-item-update-title' => !$disable_actions,
                        'flex size-7 items-center relative z-1 justify-center rounded-full border bg-background transition-all dark:bg-primary dark:text-primary-foreground dark:border-primary hover:scale-110 group-[&.edit-mode]:bg-emerald-500 group-[&.edit-mode]:border-emerald-500 group-[&.edit-mode]:text-white',
                    ])
                    @if ($disable_actions) onclick="return toastr.info({{ __('This feature is disabled in Demo version.') }})" @endif
                >
                    <x-tabler-pencil class="size-4 group-[&.edit-mode]:hidden" />
                    <x-tabler-check class="hidden size-4 group-[&.edit-mode]:block" />
                </button>

                <!-- Delete Button -->
                <button
                    @class([
                        'chat-item-delete' => !$disable_actions,
                        'flex size-7 items-center relative z-1 justify-center rounded-full border border-red-600 bg-red-600 text-white transition-all hover:scale-110 group-[&.edit-mode]:hidden',
                    ])
                    @if ($disable_actions) onclick="return toastr.info({{ __('This feature is disabled in Demo version.') }})" @endif
                >
                    <x-tabler-x class="size-4" />
                </button>
            </span>
        </li>
    @endforeach
</ul>
