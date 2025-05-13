<x-card
    id="workbook_textarea"
    @class(['w-full [&_.tox-edit-area__iframe]:!bg-transparent'])
    variant="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.variant', 'solid') }}"
    size="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.size', 'md') }}"
    roundness="{{ Theme::getSetting('defaultVariations.card.roundness', 'default') === 'default' ? 'none' : Theme::getSetting('defaultVariations.card.roundness', 'default') }}"
>
    <div class="lqd-generator-actions flex w-full flex-wrap items-center gap-3 text-2xs">
        @include('panel.user.openai.components.workbook-actions', [
            'type' => $workbook->generator->type,
            'title' => $workbook->title,
            'slug' => $workbook->slug,
            'output' => $workbook->output,
            'is_generated_doc' => true,
        ])
    </div>

    <div class="lqd-generator-form-wrap mt-4 min-h-full w-full border-t pt-6">
        @if ($workbook->generator->type === 'code')
            <input
                id="code_lang"
                type="hidden"
                value="{{ substr($workbook->input, strrpos($workbook->input, 'in') + 3) }}"
            >
            <div
                class="line-numbers min-h-full resize [direction:ltr] [&_kbd]:inline-flex [&_kbd]:rounded [&_kbd]:bg-primary/10 [&_kbd]:px-1 [&_kbd]:py-0.5 [&_kbd]:font-semibold [&_kbd]:text-primary [&_pre[class*=language]]:my-4 [&_pre[class*=language]]:rounded"
                id="code-pre"
            >
                <div
                    class="prose dark:prose-invert"
                    id="code-output"
                >{{ $workbook->output }}</div>
            </div>
        @elseif($workbook->generator->type === 'image')
            <figure>
                <a href="{{ $workbook->output }}">
                    <img
                        class="rounded-xl shadow-xl"
                        src="{{ custom_theme_url($workbook->output) }}"
                        alt="{{ __($workbook->generator->title) }}"
                    />
                </a>
            </figure>
        @elseif($workbook->generator->type === 'voiceover' || $workbook->generator->type === \App\Domains\Entity\Enums\EntityEnum::ISOLATOR->value)
            <div class="flex grow justify-end gap-2">
                <div
                    class="data-audio flex grow items-center"
                    data-audio="/uploads/{{ $workbook->output }}"
                >
                    <button type="button">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="9"
                            height="9"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                stroke="none"
                                d="M0 0h24v24H0z"
                                fill="none"
                            ></path>
                            <path
                                d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z"
                                stroke-width="0"
                                fill="currentColor"
                            ></path>
                        </svg>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="10"
                            height="10"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                stroke="none"
                                d="M0 0h24v24H0z"
                                fill="none"
                            ></path>
                            <path
                                d="M9 4h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2z"
                                stroke-width="0"
                                fill="currentColor"
                            ></path>
                            <path
                                d="M17 4h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2z"
                                stroke-width="0"
                                fill="currentColor"
                            ></path>
                        </svg>
                    </button>
                    <div class="audio-preview grow"></div>
                    <span>0:00</span>
                </div>
            </div>
        @elseif(in_array($workbook->generator->type, ['text', 'youtube', 'rss', 'audio']))
            <form
                class="workbook-form group/form flex flex-col gap-6"
                onsubmit="editWorkbook('{{ $workbook->slug }}'); return false;"
                method="POST"
            >
                <x-forms.input
                    class="border-transparent font-serif text-2xl"
                    id="workbook_title"
                    placeholder="{{ __('Untitled Document...') }}"
                    value="{{ $workbook->title }}"
                />
                <x-forms.input
                    class="tinymce border-0 font-body"
                    id="workbook_text"
                    type="textarea"
                    rows="25"
                >{!! $workbook->output !!}</x-forms.input>
                <x-button
                    class="w-full"
                    id="workbook_button"
                    tag="button"
                    type="submit"
                    variant="primary"
                    size="lg"
                >
                    <span class="group-[&.loading]/form:hidden">{{ __('Save') }}</span>
                    <span class="hidden group-[&.loading]/form:inline-block">{{ __('Please wait...') }}</span>
                </x-button>
                @csrf
            </form>
        @endif
    </div>
</x-card>
