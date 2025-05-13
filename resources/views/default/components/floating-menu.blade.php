<!-- Desktop floating add button -->
<div class="lqd-floating-menu group fixed bottom-20 end-12 isolate z-50 hidden lg:block">
    <button
        class="size-14 translate-x-0 transform-gpu overflow-hidden rounded-full border-none bg-transparent p-0 text-white shadow-lg outline-none"
        type="button"
    >
        <span
            class="spinner_button relative mb-1 inline-flex h-full w-full items-center justify-center overflow-hidden rounded-full before:absolute before:left-0 before:top-0 before:h-full before:w-full before:animate-spin-grow before:rounded-full"
        >
            <x-tabler-plus class="relative transition-transform duration-300 group-hover:rotate-[135deg]" />
        </span>
    </button>
    <div
        class="min-w-36 invisible absolute bottom-full end-0 mb-4 translate-y-2 rounded-md bg-background text-sm font-medium text-heading-foreground opacity-0 shadow-2xl shadow-black/10 transition-all before:absolute before:inset-x-0 before:-bottom-4 before:h-4 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 dark:bg-surface"
        id="add-new-floating"
    >
        <span class="block border-b border-foreground/5 px-4 py-2 text-foreground/60 last:border-0">
            {{ __('Items:') }}
        </span>
        <ul>
            @if ($setting->feature_ai_writer)
                <li class="border-b border-foreground/5 last:border-0">
                    <a
                        class="{{ activeRoute('dashboard.user.openai.list') }} relative block px-4 py-2 text-inherit before:absolute before:inset-0 before:bg-current before:text-current before:opacity-0 before:transition-opacity hover:no-underline hover:before:opacity-5 [&.active]:before:opacity-5"
                        href="{{ route('dashboard.user.openai.list') }}"
                    >
                        {{ __('Document') }}
                    </a>
                </li>
            @endif
            @if ($setting->feature_ai_image)
                <li class="border-b border-foreground/5 last:border-0">
                    <a
                        class="{{ route('dashboard.user.openai.generator', 'ai_image_generator') == url()->current() ? 'active' : '' }} relative block px-4 py-2 text-inherit before:absolute before:inset-0 before:bg-current before:text-current before:opacity-0 before:transition-opacity hover:no-underline hover:before:opacity-5 [&.active]:before:opacity-5"
                        href="{{ route('dashboard.user.openai.generator', 'ai_image_generator') }}"
                    >
                        {{ __('Image') }}
                    </a>
                </li>
            @endif
            @if ($setting->feature_ai_chat)
                <li class="border-b border-foreground/5 last:border-0">
                    <a
                        class="{{ route('dashboard.user.openai.chat.chat') == url()->current() ? 'active' : '' }} relative block px-4 py-2 text-inherit before:absolute before:inset-0 before:bg-current before:text-current before:opacity-0 before:transition-opacity hover:no-underline hover:before:opacity-5 [&.active]:before:opacity-5"
                        href="{{ route('dashboard.user.openai.chat.chat') }}"
                    >
                        {{ __('Chat') }}
                    </a>
                </li>
            @endif
            @if ($setting->feature_ai_code)
                <li class="border-b border-foreground/5 last:border-0">
                    <a
                        class="{{ route('dashboard.user.openai.generator.workbook', 'ai_code_generator') == url()->current() ? 'active' : '' }} relative block px-4 py-2 text-inherit before:absolute before:inset-0 before:bg-current before:text-current before:opacity-0 before:transition-opacity hover:no-underline hover:before:opacity-5 [&.active]:before:opacity-5"
                        href="{{ route('dashboard.user.openai.generator.workbook', 'ai_code_generator') }}"
                    >
                        {{ __('Code') }}
                    </a>
                </li>
            @endif
            @if ($setting->feature_ai_speech_to_text)
                <li class="border-b border-foreground/5 last:border-0">
                    <a
                        class="{{ route('dashboard.user.openai.generator', 'ai_speech_to_text') == url()->current() ? 'active' : '' }} relative block px-4 py-2 text-inherit before:absolute before:inset-0 before:bg-current before:text-current before:opacity-0 before:transition-opacity hover:no-underline hover:before:opacity-5 [&.active]:before:opacity-5"
                        href="{{ route('dashboard.user.openai.generator', 'ai_speech_to_text') }}"
                    >
                        {{ __('Transcribe') }}
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
