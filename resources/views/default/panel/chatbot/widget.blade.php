@php
    $page = isset($page) ? $page : 'dashboard';

    $chatbot = App\Models\Chatbot\Chatbot::query()
        ->where('id', $settings_two->chatbot_template)
        ->first();

    if (!$chatbot) {
        return;
    }

    $ipAddress = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : request()->ip();

    $db_ip_address = App\Models\RateLimit::query()->where('ip_address', $ipAddress)->where('type', 'chatbot')->first();

    $chatbot_history = App\Models\ChatBotHistory::query()->where('ip', $ipAddress)->first();

    $position = $settings_two->chatbot_position ?? 'bottom-left';

    $chatbot_custom_dimensions = '';

    switch ($position) {
        case 'top-left':
            $trigger_class = 'top-10 start-10';
            $chat_class = 'start-0 top-full';
            break;

        case 'top-right':
            $trigger_class = 'top-10 end-12';
            $chat_class = 'end-0 top-full';
            break;

        case 'bottom-right':
            $trigger_class = 'end-10 ' . ($page === 'dashboard' ? 'bottom-20 lg:bottom-16' : 'bottom-8');
            $chat_class = 'end-0 bottom-full';
            break;

        case 'bottom-left':
            $trigger_class = 'start-10 ' . ($page === 'dashboard' ? 'bottom-20 lg:bottom-16' : 'bottom-8');
            $chat_class = 'start-0 bottom-full';
            break;
    }

    if ($chatbot->width || $chatbot->height) {
        $chatbot_custom_dimensions = sprintf(
            '%s %s',
            $chatbot->width ? 'width:' . $chatbot->width . '!important;' : '',
            $chatbot->height ? 'height:' . $chatbot->height . '!important;' : '',
        );
    }
@endphp

<div class="lqd-chatbot {{ $trigger_class }} fixed z-50" data-name="{{\App\Enums\Introduction::DASHBOARD_CHATBOT_ICON}}">
    {{-- trigger --}}
    <button
        class="lqd-chatbot-trigger size-14 group relative cursor-pointer overflow-hidden rounded-full border-none bg-white p-0 text-black shadow-lg dark:!bg-[#1a1d23] dark:!text-white"
        id="chatbot-trigger"
        data-chatbot="{{ $chatbot_history != null ? $chatbot_history->user_openai_chat_id : null }}"
        type="button"
    >
        <img
            class="h-full w-full overflow-hidden rounded-full object-cover object-center transition-all group-[&.lqd-is-active]:-translate-y-2 group-[&.lqd-is-active]:scale-90 group-[&.lqd-is-active]:opacity-0"
            src="{{ $chatbot->image ? '/uploads/' . $chatbot->image : custom_theme_url(config('app.url') . '/themes/default/assets/img/chatbot-default.png') }}"
            alt=""
        >
        <span
            class="absolute start-0 top-0 inline-flex h-full w-full translate-y-2 scale-90 items-center justify-center opacity-0 transition-all group-[&.lqd-is-active]:translate-y-0 group-[&.lqd-is-active]:scale-100 group-[&.lqd-is-active]:!opacity-100"
        >
            <x-tabler-chevron-down class="size-6" />
        </span>
    </button>
    {{-- chat --}}
    <div
        class="lqd-chatbot-chats-wrap {{ $chat_class }} invisible absolute !mb-4 flex h-[clamp(50vh,720px,75vh)] w-96 max-w-[85vw] origin-bottom translate-y-2 scale-[0.95] flex-col overflow-hidden !rounded-2xl bg-white/90 text-base font-medium text-black opacity-0 shadow-[0_3px_12px_rgba(0,0,0,0.08)] backdrop-blur-md backdrop-brightness-125 transition-all before:absolute before:inset-x-0 before:-bottom-4 before:h-4 dark:bg-[#1a1d23]/90 dark:!text-white [&.lqd-is-active]:!visible [&.lqd-is-active]:translate-y-0 [&.lqd-is-active]:scale-100 [&.lqd-is-active]:!opacity-100"
        id="chatbot-wrapper"
        style="{{ $chatbot_custom_dimensions }}"
    >
        <div class="flex items-center justify-between gap-3 bg-[#9A34CD] !px-7 !py-5 shadow-[0_4px_28px_rgba(0,0,0,0.3)]">
            <div>
                <h5 class="m-0 text-[18px] font-semibold text-white opacity-70">{{ $chatbot->title }}</h5>
                <p class="m-0 text-[13px] leading-tight text-white opacity-70">{{ $chatbot->role }}</p>
            </div>
            <div class="ms-auto">
                <button
                    class="size-7 inline-flex items-center justify-center border-none bg-transparent p-0 text-white transition-all hover:scale-110 hover:!opacity-70"
                    id="chatbot-close"
                >
                    <x-tabler-x class="size-7" />
                </button>
            </div>
        </div>
        <div
            class="flex grow flex-col"
            id="chatbot-messages"
        >
            <div
                class="chats-container h-60 grow overflow-y-auto !px-6 !py-8 text-[14px] font-normal leading-5 [&_.chat-content-container]:rounded-2xl [&_.lqd-chat-avatar]:hidden [&_.lqd-chat-user-bubble_.chat-content-container]:bg-[#9A34CD] [&_.lqd-chat-user-bubble_.chat-content-container]:text-white">
            </div>
            <form
                class="sticky bottom-0 z-10 flex w-full items-end !gap-2 self-end bg-white/15 !px-5 !py-3 shadow-[0_4px_34px_rgba(0,0,0,0.05)] dark:bg-black/15"
                id="chatbot_form"
            >
                <input
                    id="chatbot_category_id"
                    type="hidden"
                    value="{{ $chatbot_history != null ? $chatbot_history->openai_chat_category_id : null }}"
                >
                <input
                    id="chatbot_chat_id"
                    type="hidden"
                    value="{{ $chatbot_history != null ? $chatbot_history->user_openai_chat_id : null }}"
                >
                <input
                    id="chatbot"
                    type="hidden"
                    value="1"
                >

                <textarea
                    class="min-h-10 m-0 w-full resize-none flex-col rounded-[26px] border-none bg-transparent p-0 !px-3 !py-3 text-inherit outline-none placeholder:text-inherit focus:border-none focus:ring-0 max-sm:max-h-[120px] max-sm:min-h-[45px] max-sm:pe-2 max-sm:ps-0 max-sm:text-[16px]"
                    id="chatbot_prompt"
                    placeholder="{{ __('Your message') }}"
                    name="chatbot_prompt"
                    rows="1"
                ></textarea>
                <button
                    class="min-h-10 inline-flex shrink-0 items-center justify-center rounded-full border-none bg-transparent p-0 font-medium text-inherit"
                    id="send_chatbot_message_button"
                    type="submit"
                >
                    {{ __('Send') }}
                </button>
            </form>
        </div>
    </div>
</div>

<template id="chat_user_bubble">
    <div class="lqd-chat-user-bubble mb-0 flex flex-row-reverse content-end gap-[8px] lg:ms-auto">
        <span class="lqd-chat-avatar text-dark">
            <span
                class="avatar h-[24px] w-[24px] shrink-0"
                style="background-image: url(/{{ auth()->check() ? Auth::user()->avatar : null }})"
            ></span>
        </span>
        <div
            class="chat-content-container group relative mb-[7px] max-w-[calc(100%-64px)] rounded-[2em] border-none bg-[#F3E2FD] text-[#090A0A] dark:bg-[#9A34CD]/30 dark:text-white">
            <div class="chat-content px-[1.5rem] py-[0.75rem]"></div>
            <div
                class="lqd-clipboard-copy-wrap group/copy-wrap pointer-events-auto invisible absolute -start-5 bottom-0 opacity-0 transition-all group-hover:!visible group-hover:!opacity-100">
                <button
                    class="lqd-clipboard-copy inline-flex h-10 w-10 items-center justify-center rounded-full border-none bg-white p-0 text-black !shadow-lg transition-all hover:-translate-y-[2px] hover:scale-110"
                    data-copy-options='{ "content": ".chat-content", "contentIn": "<.chat-content-container" }'
                    title="{{ __('Copy to clipboard') }}"
                >
                    <span class="sr-only">{{ __('Copy to clipboard') }}</span>
                    <x-tabler-copy class="size-4" />
                </button>
            </div>
        </div>
    </div>
    <p class="mb-3 text-end text-[12px]">{{ __('now') }}</p>
</template>

<template id="chat_ai_bubble">
    <div class="lqd-chat-ai-bubble group mb-0 flex content-start gap-[8px]">
        <span class="lqd-chat-avatar text-dark">
            <span
                class="avatar h-[24px] w-[24px] shrink-0"
                style="background-image: url('{{ !empty($chat->category->image) ? '/' . $chat->category->image : url('/assets/img/auth/default-avatar.png') }}')"
            ></span>
        </span>
        <div
            class="chat-content-container relative mb-[7px] min-h-[44px] max-w-[calc(100%-64px)] rounded-[2em] border-none text-[#090A0A] before:absolute before:inset-0 before:inline-block before:rounded-[2em] before:bg-[#E5E7EB] before:content-[''] group-[&.loading]:before:animate-pulse-intense dark:text-white dark:before:bg-[rgba(255,255,255,0.02)]">
            <div class="lqd-typing hidden !items-center !gap-3 !rounded-full !px-3 !py-2 !font-medium !leading-none group-[&.loading]:inline-flex">
                <div class="lqd-typing-dots !flex !items-center !gap-1">
                    <span class="lqd-typing-dot inline-block !h-1 !w-1 animate-bounce-load-more rounded-full !bg-current opacity-40 ![animation-delay:0.2s]"></span>
                    <span class="lqd-typing-dot inline-block !h-1 !w-1 animate-bounce-load-more rounded-full !bg-current opacity-60 ![animation-delay:0.3s]"></span>
                    <span class="lqd-typing-dot inline-block !h-1 !w-1 animate-bounce-load-more rounded-full !bg-current opacity-80 ![animation-delay:0.4s]"></span>
                </div>
            </div>
            <div class="chat-content-container">
                <pre
                    class="chat-content relative m-0 w-full whitespace-pre-wrap bg-transparent px-[1.5rem] py-[0.75rem] indent-0 !font-[inherit] text-[1em] text-inherit [word-break:break-word] empty:!hidden"></pre>
                <div
                    class="lqd-clipboard-copy-wrap group/copy-wrap pointer-events-auto invisible absolute -end-5 bottom-0 opacity-0 transition-all group-hover:!visible group-hover:!opacity-100">
                    <button
                        class="lqd-clipboard-copy inline-flex h-10 w-10 items-center justify-center rounded-full border-none bg-white p-0 text-black !shadow-lg transition-all hover:-translate-y-[2px] hover:scale-110"
                        data-copy-options='{ "content": ".chat-content", "contentIn": "<.chat-content-container" }'
                        title="{{ __('Copy to clipboard') }}"
                    >
                        <span class="sr-only">{{ __('Copy to clipboard') }}</span>
                        <x-tabler-copy class="size-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
    <p class="mb-3 text-[12px]">{{ __('now') }}</p>
</template>
