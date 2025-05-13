<x-modal
    class:modal-backdrop="backdrop-blur-[2px] bg-foreground/30"
    class:modal-body="max-md:p-4"
    id="share-modal"
    anchor="end"
    title="{{ __('Chat Share') }}"
>
    <x-slot:trigger
        class="h-6 max-md:inline-flex max-md:size-8 max-md:items-center max-md:justify-center max-md:rounded-full max-md:bg-background max-md:p-0 max-md:text-foreground max-md:shadow-md max-md:hover:text-primary-foreground md:h-6 md:px-2 md:py-1 md:text-2xs"
        variant="primary"
        disable-modal="{{ $app_is_demo }}"
        disable-modal-message="{{ __('This feature is disabled in Demo version.') }}"
        @click.prevent="openModal, result = ''"
        title="{{ __('Share') }}"
    >
        <x-tabler-message-share class="size-5 md:hidden" />
        <span class="max-md:hidden">
            {{ __('Share') }}
        </span>
    </x-slot:trigger>
    <x-slot:modal
        x-data="{ categoryId: '{{ $category->id }}', chatId: '{{ $chat->id }}', result: '' }"
    >
        <div class="relative">
            <x-forms.input
                class="w-full pr-10"
                name="title"
                size="lg"
                value=""
                x-model="result"
                disabled=""
                placeholder="{{ __('Please Generate Link') }}"
            />
            <x-button
                class="absolute end-1 top-0 rounded-2xl hover:translate-y-0 hover:scale-110"
                variant="ghost"
                type="button"
                @click.prevent="copyToClipboard(result)"
            >
                <svg
                    class="icon icon-tabler icons-tabler-outline icon-tabler-copy"
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path
                        stroke="none"
                        d="M0 0h24v24H0z"
                        fill="none"
                    />
                    <path
                        d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z"
                    />
                    <path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" />
                </svg>
            </x-button>
        </div>
        <div class="flex flex-wrap justify-between gap-2 pt-3 md:gap-4">
            <x-button
                class="w-full grow sm:w-[48%]"
                @click.prevent="modalOpen = false"
                variant="outline"
            >
                {{ __('Cancel') }}
            </x-button>
            <x-button
                class="w-full grow max-sm:-order-1 sm:w-[48%]"
                tag="button"
                type="submit"
                @click.prevent="fetchLink"
                x-trap="modalOpen"
            >
                {{ __('Generate New Link') }}
            </x-button>
        </div>
    </x-slot:modal>
</x-modal>
