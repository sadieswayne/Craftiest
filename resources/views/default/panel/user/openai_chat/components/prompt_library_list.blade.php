<div
    class="grid grid-cols-1 gap-4 md:grid-cols-2"
    id="lqd-prompt-list"
>
    @forelse ($promptData as $prompt)
        <div
            class="relative w-full cursor-pointer rounded-2xl p-4 shadow-[0_2px_5px_rgba(29,39,59,0.05)] transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:bg-foreground/[1%] lg:py-6 lg:ps-6"
            data-title="{{ str()->lower($prompt->title) }}"
            data-prompt="{{ str()->lower($prompt->prompt) }}"
            data-favorite="{{ $favData->pluck('item_id')->contains($prompt->id) ? 'true' : 'false' }}"
            x-init
            x-show="(searchPromptStr === '' && promptFilter === 'all') || ($el.getAttribute('data-title').includes(searchPromptStr) || $el.getAttribute('data-prompt').includes(searchPromptStr)) && ( promptFilter === 'all' || (promptFilter === 'favorite' &&  $el.getAttribute('data-favorite') === 'true') )"
        >
            <div class="w-full md:pe-14">
                <h4 class="mb-3 text-lg font-semibold">
                    {{ $prompt->title }}
                </h4>
                <p class="text-2xs font-normal">
                    {{ $prompt->prompt }}
                </p>
            </div>
            <a
                class="absolute inset-0 rounded-2xl"
                href="#"
                @click.prevent="setPrompt($el.parentElement.getAttribute('data-prompt')); promptLibraryShow = false; focusOnPrompt()"
            >
                <span class="sr-only">{{ __('Add the template') }}</span>
            </a>
            <x-favorite-button
                class="absolute end-4 top-3"
                id="{{ $prompt->id }}"
                is-favorite="{{ $favData->pluck('item_id')->contains($prompt->id) }}"
                update-url="/dashboard/user/openai/chat/update-prompt"
                @click="$el.parentElement.setAttribute('data-favorite',  $el.parentElement.getAttribute('data-favorite') === 'true' ? 'false' : 'true')"
            />
            <x-trash-button
                class="absolute end-16 top-3"
                id="{{ $prompt->id }}"
                delete-url="/dashboard/user/openai/chat/delete-prompt"
            />
        </div>
    @empty
        <h4>{{ __('No Prompts, Please input new one') }}</h4>
    @endforelse
</div>
