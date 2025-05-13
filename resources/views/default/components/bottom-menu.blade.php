<nav class="lqd-bottom-menu fixed inset-x-0 bottom-0 z-50 hidden h-16 flex-wrap border-t bg-background/10 text-2xs font-medium backdrop-blur-md backdrop-saturate-150 max-lg:flex">
    <ul class="grid w-full grid-cols-4 place-items-center">
        <li class="w-full">
            <a
                class="flex flex-col items-center text-inherit"
                href="{{ route('dashboard.user.openai.chat.chat') }}"
            >
                <x-tabler-message />
            </a>
        </li>
        <li class="w-full">
            <a
                class="flex flex-col items-center text-inherit"
                href="{{ route('dashboard.user.openai.documents.all') }}"
            >
                <x-tabler-clipboard-text />
            </a>
        </li>
        <li class="w-full">
            <button
                class="group flex h-auto w-full flex-col items-center text-inherit"
                type="button"
                x-init
                @click.prevent="$store.mobileNav.toggleSearch()"
                :class="{ 'lqd-is-active': !$store.mobileNav.searchCollapse }"
            >
                <x-tabler-search />
            </button>
        </li>
        <li class="w-full">
            <button
                class="group flex h-auto w-full translate-x-0 transform-gpu flex-col items-center rounded-full bg-none text-inherit text-white outline-none"
                type="button"
                x-init
                @click.prevent="$store.mobileNav.toggleTemplates()"
                :class="{ 'lqd-is-active': !$store.mobileNav.templatesCollapse }"
            >
                <span
                    class="relative mb-1 inline-flex h-[30px] w-[30px] items-center justify-center overflow-hidden rounded-full before:absolute before:left-0 before:top-0 before:h-full before:w-full before:animate-spin-grow before:rounded-full before:bg-gradient-to-r before:from-[#8d65e9] before:via-[#5391e4] before:to-[#6bcd94]"
                >
                    <x-tabler-plus class="size-5 relative rotate-0 transition-transform duration-300 group-[.lqd-is-active]:rotate-[135deg]" />
                </span>
            </button>
        </li>
    </ul>
</nav>

<div
    class="invisible fixed bottom-16 right-0 z-[99] max-h-[calc(85vh-4rem)] w-full origin-bottom translate-y-2 scale-95 overflow-y-auto overscroll-contain rounded-t-2xl bg-[#fff] opacity-0 shadow-[-5px_-10px_30px_rgba(0,0,0,0.07)] transition-all dark:bg-zinc-800 lg:!hidden [&.lqd-is-active]:visible [&.lqd-is-active]:translate-y-0 [&.lqd-is-active]:scale-100 [&.lqd-is-active]:opacity-100"
    x-init
    :class="{ 'lqd-is-active': !$store.mobileNav.templatesCollapse }"
>
    <ul class="relative h-full text-2xs font-medium text-heading-foreground">
        @foreach ($aiWriters as $aiWriter)
            <li class="relative">
                <a
                    class="flex items-center gap-2 border-b border-l-0 border-r-0 border-t-0 border-solid border-[--tblr-border-color] p-3 py-2 text-inherit"
                    @if (($aiWriter->type == 'text' || $aiWriter->type == 'code') && $aiWriter->slug != 'ai_webchat') href="{{  (route('dashboard.user.openai.generator.workbook', $aiWriter->slug)) }}"
					@elseif ($aiWriter->slug == 'ai_webchat' && \Illuminate\Support\Facades\Route::has('dashboard.user.openai.webchat.workbook'))
           	 		href="{{ route('dashboard.user.openai.webchat.workbook') }}"
					@else href="{{  (route('dashboard.user.openai.generator', $aiWriter->slug)) }}" @endif
                >
                    <span
                        class="size-9 [&_svg]:size-5 relative inline-flex items-center justify-center rounded-full transition-all duration-300"
                        style="background: {{ $aiWriter->color }}"
                    >
                        <span class="inline-block transition-all duration-300">
                            {!! html_entity_decode(stripslashes($aiWriter->image)) !!}
                        </span>
                    </span>
                    {{ $aiWriter->title }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
