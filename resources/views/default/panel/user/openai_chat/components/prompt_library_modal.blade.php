<template
    x-show="promptLibraryShow"
    x-teleport="body"
>
    <div
        class="lqd-modal-modal invisible fixed start-0 top-0 z-[100] flex h-screen w-screen items-center justify-center overflow-hidden overscroll-contain opacity-0 transition-opacity"
        :class="{ 'opacity-0 invisible': !promptLibraryShow }"
        @keyup.escape="promptLibraryShow = false"
    >
        <div
            class="lqd-modal-backdrop fixed inset-0 bg-black/5 backdrop-blur-sm"
            @click="promptLibraryShow = false"
        ></div>

        <div class="container max-w-[1025px]">
            <div
                class="lqd-modal-content relative z-50 max-h-[95vh] min-h-[550px] min-w-[min(calc(100%-2rem),1025px)] overflow-y-auto overscroll-contain rounded-xl bg-background shadow-2xl shadow-black/10">
                <div
                    class="lqd-modal-body p-4"
                    x-trap.inert="promptLibraryShow"
                >
                    <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-6 rounded-xl border p-4">
                        <h4 class="m-0">
                            {{ __('Prompt Library') }}
                        </h4>

                        <x-modal
                            class:modal-backdrop="backdrop-blur-none bg-foreground/15"
                            type="inline"
                            anchor="end"
                        >
                            <x-slot:trigger
                                class="gap-1 font-bold text-primary"
                                variant="link"
                                disable-modal="{{ $app_is_demo }}"
                                disable-modal-message="{{ __('This feature is disabled in Demo version.') }}"
                            >
                                {{ __('Add') }}
                                <x-tabler-plus
                                    class="size-3"
                                    stroke-width="3"
                                />
                            </x-slot:trigger>

                            <x-slot:modal
                                x-data="{}"
                            >
                                <form
                                    class="flex flex-col gap-4"
                                    action='/dashboard/user/openai/chat/add-prompt'
                                    method="POST"
                                    x-init
                                    x-target="lqd-prompt-list"
                                    @submit="modalOpen = false;"
                                >
                                    <x-forms.input
                                        name="title"
                                        size="lg"
                                        placeholder="{{ __('Add Title') }}"
                                    />
                                    <x-forms.input
                                        name="prompt"
                                        type="textarea"
                                        rows=6
                                        placeholder="{{ __('Add custom prompt') }}"
                                    />
                                    @if (auth()->user()->isAdmin())
                                        <x-forms.input
                                            class:container="mb-2"
                                            id="show_for_all"
                                            name="show_for_all"
                                            type="checkbox"
                                            label="{{ __('Show this prompt for all users') }}"
                                            switcher
                                        />
                                    @endif
                                    <div class="flex gap-4 border-t pt-3 text-end">
                                        <x-button
                                            class="grow basis-1/2"
                                            @click.prevent="modalOpen = false"
                                            variant="outline"
                                        >
                                            {{ __('Cancel') }}
                                        </x-button>
                                        <x-button
                                            class="grow basis-1/2"
                                            tag="button"
                                            type="submit"
                                        >
                                            {{ __('Add') }}
                                        </x-button>
                                    </div>
                                </form>
                            </x-slot:modal>
                        </x-modal>
                    </div>

                    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 py-3 sm:flex-nowrap">
                        <ul class="lqd-filter-list mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-heading-foreground max-sm:gap-3">
                            @foreach ($prompt_filters as $value => $label)
                                <li>
                                    <x-button
                                        class="lqd-filter-btn inline-flex rounded-full px-2.5 py-1 text-2xs font-semibold leading-tight transition-colors hover:translate-y-0 hover:bg-foreground/5 [&.active]:bg-secondary [&.active]:text-secondary-foreground"
                                        tag="button"
                                        type="button"
                                        name="filter"
                                        variant="ghost"
                                        filter="all"
                                        x-data="{}"
                                        ::class="promptFilter === '{{ $value }}' && 'active'"
                                        @click="changePromptFilter('{{ $value }}')"
                                    >
                                        {{ __($label) }}
                                    </x-button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="relative w-full lg:w-56">
                            <x-forms.input
                                class="navbar-search-input rounded-full bg-clay ps-11 placeholder:text-heading-foreground"
                                id="search_str"
                                type="search"
                                placeholder="{{ __('Search') }}"
                                aria-label="{{ __('Search in website') }}"
                                {{-- @keyup="setSearchPromptStr( $event.target.value )" --}}
                                x-model="searchPromptStr"
                                ::bind="searchPromptStr"
                            />
                            <x-tabler-search class="pointer-events-none absolute start-4 top-1/2 size-5 -translate-y-1/2" />
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-4 md:grid-cols-2"
                        id="lqd-prompt-list"
                        x-init="$ajax('/dashboard/user/openai/chat/prompts', { method: 'POST' })"
                    >
                    </div>

                    {{-- <div
						class="px-6 py-11 text-center"
						id="no_prompt"
					>
						<h4 class="font-semibold">
							{{ __('No Prompts, Please input new one') }}
						</h4>
					</div>

					<div
						class="mx-2 flex max-h-[550px] flex-wrap justify-between gap-y-6 overflow-scroll p-3"
						id="prompts"
					>
					</div> --}}
                </div>
            </div>
        </div>
    </div>
</template>
