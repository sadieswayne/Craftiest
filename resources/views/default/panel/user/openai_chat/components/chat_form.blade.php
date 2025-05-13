@php
    $prompt_filters = [
        'all' => __('All'),
        'favorite' => __('Favorite'),
    ];
@endphp

<div
    class="lqd-chat-form-wrap sticky -bottom-px z-30 rounded-b-[inherit]"
    x-data="{
        promptLibraryShow: false,
        togglePromptLibraryShow() { this.promptLibraryShow = !this.promptLibraryShow },
        promptFilter: 'all',
        changePromptFilter(filter) { filter !== this.promptFilter && (this.promptFilter = filter) },
        searchPromptStr: '',
        setSearchPromptStr(str) { this.searchPromptStr = str.trim().toLowerCase() },
        prompt: '',
        setPrompt(prompt) { this.prompt = prompt },
        focusOnPrompt() { $nextTick(() => $refs.prompt.focus()) }
    }"
>
    {{-- using form element cause issues in webchat after analyzing a website --}}
    <div
        class="lqd-chat-form flex w-full items-end gap-3 self-end rounded-ee-[inherit] bg-background/95 p-8 py-6 backdrop-blur-lg backdrop-saturate-150 max-md:items-end max-md:p-4 max-sm:p-3"
        id="chat_form"
    >
        @csrf
        <input
            id="category_id"
            type="hidden"
            value="{{ $category->id }}"
        />
        <input
            id="chat_id"
            type="hidden"
            value="{{ isset($chat) ? $chat->id : null }}"
        />
        <div class="lqd-chat-form-inputs-container flex min-h-[52px] w-full flex-col rounded-[26px] border border-input-border max-md:min-h-[45px]">
            <div
                class="hidden max-h-32 w-full grid-cols-3 gap-5 overflow-y-auto p-2.5 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 [&.active]:grid"
                id="chat_images"
            ></div>

            <div
                class="hidden max-h-32 w-full grid-cols-3 gap-5 overflow-y-auto p-2.5 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 [&.active]:grid"
                id="chat_pdfs"
            ></div>

            <hr class="split_line border-1 mb-2.5 hidden w-full" />

            <div class="relative flex grow items-center">
                <input
                    id="selectImageInput"
                    type="file"
                    style="display: none;"
                    @if ($category->slug != 'ai_vision' && $category->slug != 'ai_pdf') accept="image/*" @endif
                />

                <x-button
                    class="lqd-chat-mobile-options-trigger ms-1 mt-[3px] size-8 shrink-0 origin-center transition-transform md:hidden [&.active]:rotate-45"
                    @click.prevent="toggleMobileOptions()"
                    @click.outside="mobileOptionsShow && (mobileOptionsShow = false)"
                    ::class="{ 'active': mobileOptionsShow }"
                    size="none"
                    variant="ghost"
                    tag="button"
                >
                    <x-tabler-plus class="size-4" />
                    <span class="sr-only">{{ __('Options') }}</span>
                </x-button>

                <x-forms.input
                    id="prompt"
                    @class([
                        'm-0 w-full border-none bg-transparent py-3 pe-[100px] text-heading-foreground focus:outline-none focus:ring-0 max-md:max-h-[200px] max-md:pe-2 max-md:ps-0 max-md:text-[16px]',
                        'ps-16' => $category->slug !== 'ai_pdf',
                    ])
                    container-class="w-full"
                    type="textarea"
                    placeholder="{{ __('Type a message') }}"
                    name="prompt"
                    rows="1"
                    x-model="prompt"
                    x-ref="prompt"
                    ::bind="prompt"
                />

                <div class="pointer-events-none absolute bottom-0 end-2 start-2 flex items-end justify-between py-[5px] text-sm max-md:static">
                    <div
                        class="flex grow items-center justify-between max-md:invisible max-md:absolute max-md:-end-12 max-md:-start-1 max-md:bottom-full max-md:mb-3 max-md:translate-y-1 max-md:scale-95 max-md:flex-col max-md:items-start max-md:gap-4 max-md:rounded-xl max-md:bg-background max-md:px-4 max-md:py-0 max-md:opacity-0 max-md:shadow-lg max-md:transition-all md:flex md:h-full max-md:[&.active]:visible max-md:[&.active]:translate-y-0 max-md:[&.active]:scale-100 max-md:[&.active]:opacity-100"
                        id="chat-options"
                        :class="{ 'active': mobileOptionsShow }"
                    >
                        <div @class([
                            'pointer-events-auto max-md:pt-4',
                            'flex items-center' => $category->slug !== 'ai_pdf',
                            'hidden' => $category->slug === 'ai_pdf',
                        ])>
                            <button
                                class="lqd-chat-attach max-md:!text-heading flex size-10 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full bg-secondary text-secondary-foreground transition-all max-md:h-auto max-md:w-auto max-md:bg-transparent max-md:text-heading-foreground md:hover:bg-secondary md:hover:opacity-80"
                                type="button"
                                @if ($app_is_demo) onclick='return toastr.info("@lang('This feature is disabled in Demo version.')")' @else id="chat_add_image" @endif
                            >
                                <x-tabler-paperclip
                                    class="size-5"
                                    stroke-width="1.5"
                                />
                                <span class="md:hidden">{{ __('Upload a document or image') }}</span>
                            </button>
                        </div>

                        <div class="max-md:hidden md:ms-auto"></div>

                        @includeIf('openai-realtime-chat::chat-button', ['compact' => true, 'category_slug' => $category->slug, 'messages' => $chat->messages])

                        @if (setting('user_prompt_library') == null || setting('user_prompt_library'))
                            <div @class([
                                'pointer-events-auto flex items-center max-md:flex-col max-md:items-start max-md:gap-4',
                                'max-md:pt-4' => $category->slug === 'ai_pdf',
                            ])>
                                <x-button
                                    class="lqd-chat-templates-trigger flex size-10 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full text-heading-foreground transition-all max-md:h-auto max-md:w-auto max-md:bg-transparent md:hover:bg-secondary md:hover:text-secondary-foreground"
                                    type="button"
                                    variant="ghost"
                                    size="none"
                                    @click.prevent="togglePromptLibraryShow()"
                                >
                                    <x-tabler-article
                                        class="size-6"
                                        stroke-width="1.5"
                                    />
                                    <span class="md:hidden">
                                        {{ __('Browse prompt library') }}
                                    </span>
                                </x-button>
                            </div>
                        @endif
                        {{-- Brand Voice --}}
                        <div class="pointer-events-auto flex items-center max-md:flex-col max-md:items-start max-md:gap-4 max-md:pb-4">
                            <x-modal
                                class="lqd-chat-brand-voice"
                                id="brandVoiceModal"
                                title="{{ __('Brand Voice') }}"
                            >
                                <x-slot:trigger
                                    class="lqd-chat-brand-voice-trigger flex size-10 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full p-0 text-heading-foreground transition-all max-md:h-auto max-md:w-auto max-md:bg-transparent md:hover:bg-secondary md:hover:text-secondary-foreground"
                                    variant="none"
                                >
                                    <x-tabler-brand-trello
                                        class="size-6"
                                        stroke-width="1.5"
                                    />
                                    <span class="md:hidden">
                                        {{ __('Brand Voice') }}
                                    </span>
                                </x-slot:trigger>

                                <x-slot:modal
                                    x-data="{}"
                                >
                                    <div class="flex flex-col gap-6">
                                        <x-forms.input
                                            id="chat_brand_voice"
                                            type="select"
                                            size="lg"
                                            name="chat_brand_voice"
                                            label="{{ __('Select Company') }}"
                                            onchange="getProductByBrand(this.value)"
                                        >
                                            <option value="">
                                                {{ __('Select Company') }}
                                            </option>
                                            @foreach (auth()->user()->getCompanies() ?? [] as $company)
                                                <option
                                                    data-tone_of_voice="{{ $company->tone_of_voice }}"
                                                    value="{{ $company->id }}"
                                                >
                                                    {{ $company->name }}
                                                </option>
                                            @endforeach
                                        </x-forms.input>

                                        <x-forms.input
                                            id="brand_voice_prod"
                                            type="select"
                                            size="lg"
                                            name="brand_voice_prod"
                                            label="{{ __('Select Product / Service') }}"
                                        >
                                            <option value="">{{ __('Select Product') }}</option>
                                        </x-forms.input>

                                        <div class="border-t pt-3 text-end">
                                            <x-button
                                                @click.prevent="modalOpen = false"
                                                type="button"
                                                variant="outline"
                                            >
                                                {{ __('Cancel') }}
                                            </x-button>

                                            <x-button
                                                type="button"
                                                @click.prevent="modalOpen = false"
                                                onclick="setBrandVoice()"
                                            >
                                                {{ __('Done') }}
                                            </x-button>
                                        </div>
                                    </div>
                                </x-slot:modal>
                            </x-modal>
                        </div>
                    </div>

                    {{-- Record Audio --}}
                    <div class="pointer-events-auto max-md:absolute max-md:bottom-[10px] max-md:end-2">
                        <x-button
                            class="lqd-chat-record-trigger flex size-10 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full text-heading-foreground transition-all max-md:h-auto max-md:w-auto max-md:bg-transparent md:hover:bg-secondary md:hover:text-secondary-foreground [&.inactive]:hidden"
                            id="voice_record_button"
                            type="button"
                            variant="none"
                            size="none"
                            title="{{ __('Record audio') }}"
                        >
                            <x-tabler-microphone
                                class="size-6"
                                stroke-width="1.5"
                            />
                        </x-button>
                        <x-button
                            class="lqd-chat-record-stop-trigger hidden size-10 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full text-heading-foreground transition-all max-md:h-auto max-md:w-auto max-md:bg-transparent md:hover:bg-secondary md:hover:text-secondary-foreground [&.active]:flex"
                            id="voice_record_stop_button"
                            type="button"
                            variant="none"
                            size="none"
                            title="{{ __('Stop recording') }}"
                        >
                            <x-tabler-player-pause-filled
                                class="size-5"
                                stroke-width="1.5"
                            />
                        </x-button>
                    </div>
                </div>
            </div>
        </div>

        <input
            id="assistant"
            type="hidden"
            value="{{ $category->assistant }}"
        />
        <input
            id="chatbot_id"
            type="hidden"
            value="{{ $category->chatbot_id }}"
        />
        <input
            id="category_id"
            type="hidden"
            value="{{ $category->id }}"
        />
        <input
            id="chat_id"
            type="hidden"
            value="{{ isset($chat) ? $chat->id : null }}"
        />
        <x-button
            class="lqd-chat-send-btn aspect-square size-[52px] shrink-0 max-md:size-10 max-md:min-h-[45px] max-md:min-w-[45px]"
            id="{{ $category->slug == 'ai_vision' && $app_is_demo ? '' : 'send_message_button' }}"
            size="none"
            tag="button"
            onclick="{!! $category->slug == 'ai_vision' && $app_is_demo ? 'return toastr.info(\'{{ __('This feature is disabled in Demo version.') }}\')' : '' !!}"
            type="submit"
        >
            <x-tabler-send-2
                class="size-6 rtl:-scale-x-100"
                stroke-width="1.5"
            />
        </x-button>
        <x-button
            class="lqd-chat-stop-btn hidden aspect-square size-[52px] shrink-0 max-md:size-10 max-md:min-h-[45px] max-md:min-w-[45px] [&.active]:flex"
            id="stop_button"
            size="none"
            tag="button"
        >
            <x-tabler-hand-stop
                class="size-6"
                stroke-width="1.5"
            />
        </x-button>

    </div>

    @include('panel.user.openai_chat.components.prompt_library_modal')
</div>
