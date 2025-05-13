<ul class="chat-list-ul list-none m-0 p-0 text-[14px] h-full overflow-auto">
    @foreach ($list as $entry)
        <li class="chat-list-item border-b border-solid border-[--tblr-border-color] border-t-0 border-r-0 border-l-0 relative [&.active]:before:content-[''] [&.active]:before:w-[3px] [&.active]:before:h-[50%] [&.active]:before:absolute [&.active]:before:top-[25%] [&.active]:before:left-0 [&.active]:before:bg-gradient-to-b [&.active]:before:from-[--tblr-primary] [&.active]:before:to-transparent overflow-hidden group
		@if (isset($chat)) {{ $chat->id == $entry->id ? 'active' : '' }} @endif"
            id="chat_{{ $entry->id }}">
            <div onclick="return openChatAreaContainer({{ $entry->id }});"
                class="flex gap-3 text-[--lqd-heading-color] p-[20px] hover:no-underline hover:text-[--tblr-primary] group-[&.edit-mode]:pointer-events-none cursor-pointer items-center">
                <span class="shrink-0">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 10V10.0108M5.66699 10V10.0108M14.333 10V10.0108M1 19L2.29899 14.6183C1.17631 12.7513 0.770172 10.5404 1.1561 8.39656C1.54202 6.25276 2.69374 4.32196 4.39712 2.96318C6.1005 1.6044 8.23963 0.910088 10.4168 1.00934C12.5939 1.1086 14.6609 1.99466 16.2334 3.50279C17.806 5.01092 18.777 7.03849 18.9661 9.20851C19.1551 11.3785 18.5493 13.5433 17.2612 15.3004C15.9731 17.0575 14.0905 18.2872 11.9633 18.7611C9.83606 19.2349 7.60906 18.9206 5.69635 17.8765L1 19Z"
                            stroke="var(--lqd-heading-color)" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
                <span class="flex flex-col  overflow-hidden">
                    <span
                        class="chat-item-title max-w-[100px] group-[&.edit-mode]:pointer-events-auto text-nowrap !overflow-hidden text-ellipsis"
                        title="{{ __($entry->title) }} ">{{ __($entry->title) }}</span>
                    <a class="chat-item-title max-w-[100px] group-[&.edit-mode]:pointer-events-auto text-nowrap !overflow-hidden text-ellipsis"
                        href="title="{{ __($entry->website_url) }}" title="{{ __($entry->website_url) }} ">{{ __($entry->website_url) }}</a>
                    <span class="text-muted text-[11px]">{{ $entry->updated_at->diffForHumans() }}</span>
                </span>
            </div>
            <span class="absolute -translate-y-1/2 top-1/2 end-4">
                <button
                    @if (
                        $app_is_demo &&
                            (isset($category) &&
                                ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'))) onclick="return toastr.info('This feature is disabled in Demo version.')" @endif
                    class="@if (
                        $app_is_demo &&
                            (isset($category) &&
                                ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'))) @else  chat-item-update-title @endif btn bg-[--tblr-body-bg] w-[28px] h-[28px] p-0 border-[1px] border-solid border-[--tblr-border-color] opacity-0 group-hover:!opacity-100 group-[&.edit-mode]:!opacity-100 group-[&.edit-mode]:!bg-[--tblr-green]  dark:bg-[rgba(255,255,255,0.08)] max-sm:!opacity-100">
                    <svg class="group-[&.edit-mode]:hidden" width="13" height="11" viewBox="0 0 13 11"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M1.5293 10.2941H4.15418L11.0445 3.7664C11.3926 3.43664 11.5881 2.98938 11.5881 2.52303C11.5881 2.05668 11.3926 1.60943 11.0445 1.27967C10.6964 0.949906 10.2243 0.764648 9.73205 0.764648C9.23979 0.764648 8.76769 0.949906 8.41961 1.27967L1.5293 7.80733V10.2941Z"
                            stroke="currentColor" stroke-width="1.15" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8.23535 1.82349L10.4706 3.94113" stroke="currentColor" stroke-width="1.15"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <svg class="hidden group-[&.edit-mode]:block" xmlns="http://www.w3.org/2000/svg" width="18"
                        height="18" viewBox="0 0 24 24" stroke-width="2" stroke="white" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12l5 5l10 -10"></path>
                    </svg>
                </button>
                <button
                    @if (
                        $app_is_demo &&
                            (isset($category) &&
                                ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'))) onclick="return toastr.info('This feature is disabled in Demo version.')" @endif
                    class="@if (
                        $app_is_demo &&
                            (isset($category) &&
                                ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'))) @else chat-item-delete @endif btn bg-red w-[28px] h-[28px] p-0 border-[1px] border-solid border-red opacity-0 group-hover:!opacity-100 group-[&.edit-mode]:hidden max-sm:!opacity-100">
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="white"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z">
                        </path>
                    </svg>
                </button>
            </span>
        </li>
    @endforeach
</ul>
