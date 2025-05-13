@php
    $models = \App\Extensions\OpenRouter\System\Enums\OpenRouterEngine::cases();
@endphp

<div x-data="{
    selectedModelValue: '',
    selectedModelLabel: '',
    selectedCard: null,
    searchString: '',
    selectCard(ev) {
        const el = event.currentTarget;
        this.selectedCard = el;
    },
    saveChanges() {
        if (!this.selectedCard) return;

        const modelValue = this.selectedCard.getAttribute('data-model-value');
        const modelLabel = this.selectedCard.getAttribute('data-model-label');
        const modelSelectElement = this.$refs.modelsSelectElement;

        this.selectedModelValue = modelValue;
        this.selectedModelLabel = modelLabel;

        modelSelectElement.value = modelValue;
    }
}">
    <x-modal
        class:modal-head="gap-1 sticky top-0 z-50 bg-background p-4"
        class:modal-content="mx-5"
        class:close-btn="-order-1 sm:order-1 ms-0"
        id="openRouterModel"
    >
        <x-slot:trigger
            variant="ghost-shadow"
        >
            <svg
                width="18"
                height="16"
                viewBox="0 0 18 16"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M17.007 6.15216L15.615 6.43416V6.43616C14.9445 6.57221 14.329 6.90274 13.8453 7.38647C13.3616 7.87021 13.031 8.48572 12.895 9.15617L12.613 10.5482C12.5832 10.6852 12.5075 10.8079 12.3983 10.8958C12.2892 10.9838 12.1532 11.0318 12.013 11.0318C11.8728 11.0318 11.7368 10.9838 11.6277 10.8958C11.5185 10.8079 11.4427 10.6852 11.413 10.5482L11.131 9.15617C10.9952 8.48561 10.6647 7.86997 10.181 7.38619C9.69718 6.90241 9.08153 6.57197 8.41098 6.43616L7.01898 6.15416C6.88017 6.12574 6.75543 6.05026 6.66585 5.94048C6.57627 5.8307 6.52734 5.69336 6.52734 5.55166C6.52734 5.40997 6.57627 5.27263 6.66585 5.16285C6.75543 5.05307 6.88017 4.97759 7.01898 4.94916L8.41098 4.66716C9.08148 4.53124 9.69706 4.20076 10.1808 3.717C10.6646 3.23324 10.9951 2.61766 11.131 1.94716L11.413 0.555164C11.4427 0.418164 11.5185 0.295476 11.6277 0.207494C11.7368 0.119511 11.8728 0.0715332 12.013 0.0715332C12.1532 0.0715332 12.2892 0.119511 12.3983 0.207494C12.5075 0.295476 12.5832 0.418164 12.613 0.555164L12.895 1.94716C13.031 2.61761 13.3616 3.23312 13.8453 3.71686C14.329 4.20059 14.9445 4.53112 15.615 4.66716L17.007 4.94716C17.1458 4.97559 17.2705 5.05107 17.3601 5.16085C17.4497 5.27063 17.4986 5.40797 17.4986 5.54966C17.4986 5.69136 17.4497 5.8287 17.3601 5.93848C17.2705 6.04826 17.1458 6.12374 17.007 6.15216ZM6.82915 13.2051L6.45115 13.2821C5.98493 13.3767 5.55688 13.6054 5.22041 13.9417C4.88394 14.278 4.65395 14.706 4.55915 15.1721L4.48215 15.5501C4.46155 15.6566 4.40452 15.7526 4.32086 15.8217C4.2372 15.8907 4.13211 15.9284 4.02365 15.9284C3.91518 15.9284 3.8101 15.8907 3.72644 15.8217C3.64278 15.7526 3.58575 15.6566 3.56515 15.5501L3.48815 15.1721C3.39347 14.7059 3.16352 14.2779 2.82703 13.9416C2.49054 13.6053 2.06242 13.3756 1.59615 13.2811L1.21815 13.2041C1.11166 13.1835 1.01566 13.1265 0.946629 13.0428C0.877599 12.9592 0.839844 12.8541 0.839844 12.7456C0.839844 12.6372 0.877599 12.5321 0.946629 12.4484C1.01566 12.3648 1.11166 12.3077 1.21815 12.2871L1.59615 12.2101C2.06256 12.1156 2.49077 11.8858 2.82727 11.5493C3.16378 11.2128 3.39364 10.7845 3.48815 10.3181L3.56515 9.94013C3.58575 9.83364 3.64278 9.73764 3.72644 9.66861C3.8101 9.59958 3.91518 9.56183 4.02365 9.56183C4.13211 9.56183 4.2372 9.59958 4.32086 9.66861C4.40452 9.73764 4.46155 9.83364 4.48215 9.94013L4.55915 10.3181C4.6536 10.7847 4.88343 11.213 5.21992 11.5497C5.55642 11.8864 5.98466 12.1164 6.45115 12.2111L6.82915 12.2881C6.93564 12.3087 7.03164 12.3658 7.10067 12.4494C7.1697 12.5331 7.20745 12.6382 7.20745 12.7466C7.20745 12.8551 7.1697 12.9602 7.10067 13.0438C7.03164 13.1275 6.93564 13.1845 6.82915 13.2051Z"
                />
            </svg>
            <span>
                @lang('AI Model: ')
                <span x-text="selectedModelLabel && selectedModelLabel !== '' ? selectedModelLabel : '@lang('None')'"></span>
            </span>
        </x-slot:trigger>

        <x-slot:head-content>
            <div class="flex flex-wrap justify-between gap-y-3 sm:grow sm:flex-nowrap">
                <div class="grow">
                    <h4 class="mb-0">
                        @lang('AI Models')
                    </h4>
                    <p class="mb-0 text-2xs font-normal text-foreground">
                        @lang('Choose the AI model that best suits your needs.')
                    </p>
                </div>

                <div class="lg:ms-auto">
                    <form action="#">
                        <x-forms.input
                            class="max-h-9 rounded-full bg-clay ps-8 sm:min-w-64"
                            type="search"
                            placeholder="{{ __('Search model') }}"
                            @input="searchString = $event.target.value"
                        >
                            <x-slot:icon>
                                <x-tabler-search class="absolute start-3 top-1/2 size-4 -translate-y-1/2" />
                            </x-slot:icon>
                        </x-forms.input>
                    </form>
                </div>
            </div>
        </x-slot:head-content>

        <x-slot:modal>
            <div>
                <form
                    action="#"
                    @submit.prevent="saveChanges(); modalOpen = false;"
                >
                    <div class="flex flex-col items-end">
                        <div class="grid w-full grid-cols-1 gap-7 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($models as $engine)
                                <x-card
                                    class="model-card cursor-pointer data-[selected]:outline data-[selected]:outline-[3px] data-[selected]:outline-secondary"
                                    class:body="md:p-7 p-5"
                                    data-model-value="{{ $engine->value }}"
                                    data-model-label="{{ $engine->label() }}"
                                    variant="outline-shadow"
                                    @click.prevent="selectCard"
                                    ::data-selected="selectedCard?.getAttribute('data-model-value') === $el.getAttribute('data-model-value')"
                                    x-show="searchString === '' || $el.getAttribute('data-model-label').toLowerCase().includes(searchString.toLowerCase())"
                                >
                                    <div class="w-full">
                                        <div class="mb-6 flex justify-between gap-1.5">
                                            <figure class="inline-grid size-10 shrink-0 place-content-center rounded-full bg-heading-foreground/5">
                                                {{-- TODO: Add image --}}
                                                <img
                                                    class="h-auto w-6"
                                                    src=""
                                                    {{-- alt="{{ $engine->label() }}" --}}
                                                />
                                            </figure>
                                            <div class="text-end">
                                                {{-- TODO: Add needed stuff here --}}
                                                <p class="m-0 text-3xs font-medium text-heading-foreground">
                                                    @lang('Words')
                                                </p>
                                                <p class="m-0 text-3xs font-medium text-heading-foreground/50">
                                                    @lang('No credits left')
                                                </p>
                                            </div>
                                        </div>

                                        <div>
                                            <h4 class="mb-2">
                                                {{ $engine->label() }}
                                            </h4>
                                            {{-- TODO: Add description --}}
                                            <p class="mb-0">
                                                @lang('It is an advanced AI model with 16k context length and self-supervision feature.')
                                            </p>
                                        </div>
                                    </div>
                                </x-card>
                            @endforeach
                        </div>

                        <x-forms.input
                            id="open_router_model"
                            container-class="hidden"
                            name="open_router_model"
                            type="select"
                            x-ref="modelsSelectElement"
                        >
                            <option value="">{{ __('Select Model') }}</option>
                            @foreach ($models as $engine)
                                <option value="{{ $engine->value }}">
                                    {{ $engine->label() }}
                                </option>
                            @endforeach
                        </x-forms.input>

                        <x-button
                            class="sticky bottom-5 mt-10 w-full backdrop-blur-lg disabled:bg-heading-foreground/30 disabled:text-header-background"
                            type="submit"
                            size="xl"
                            ::disabled="!selectedCard"
                        >
                            {{ __('Apply') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </x-slot:modal>
    </x-modal>
</div>
