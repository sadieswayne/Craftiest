@php
    $creativity_levels = [
        '0.25' => 'Economic',
        '0.5' => 'Average',
        '0.75' => 'Good',
        '1' => 'Premium',
    ];

    $voice_tones = ['Professional', 'Funny', 'Casual', 'Excited', 'Witty', 'Sarcastic', 'Feminine', 'Masculine', 'Bold', 'Dramatic', 'Grumpy', 'Secretive', 'other'];

    $card_variant = Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.variant', 'solid');
    $card_size = Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.size', 'md');
    $card_roundness = Theme::getSetting('defaultVariations.card.roundness', 'default') === 'default' ? 'none' : Theme::getSetting('defaultVariations.card.roundness', 'default');

    if ($openai->type == 'audio') {
        if ($card_variant === 'none') {
            $card_variant = 'outline';
        }
        if ($card_size === 'none') {
            $card_size = 'md';
        }
        if ($card_roundness === 'none') {
            $card_roundness = 'lg';
        }
    }
@endphp

<div
    class="lqd-generator-wrap grid grid-flow-row gap-y-8 lg:grid-flow-col lg:[grid-template-columns:40%_60%] xl:[grid-template-columns:33%_67%]"
    data-generator-type="{{ $openai->type }}"
>
    <div class="flex w-full flex-col gap-6 lg:pe-10">
        <x-card class="lqd-generator-remaining-credits">
            <h5 class="mb-3 text-xs font-normal">
                {{ __('Remaining Credits') }}
            </h5>

            <x-credit-list />
        </x-card>

        @if ($openai->type != 'image')
            <x-card class="lqd-generator-options-card relative border-2 border-dashed">
                <form
                    class="lqd-generator-form flex flex-col gap-5"
                    id="openai_generator_form"
                    onsubmit="return sendOpenaiGeneratorForm();"
                    enctype="multipart/form-data"
                    x-data=""
                >
                    @if ($openai->type === 'audio')
                        <div class="w-full text-center text-sm">
                            <x-tabler-volume
                                class="mx-auto mb-2 size-8"
                                stroke-width="1.5"
                            />
                            <p class="m-0 font-medium">
                                <span class="opacity-70">
                                    @lang('Drag and drop an audio file')
                                </span>
                            </p>
                            <p
                                class="file-name m-0 font-medium [overflow-wrap:anywhere]"
                                x-ref="fileName"
                            >
                                @lang('or click here to browse your files.')
                            </p>
                        </div>
                    @endif
                    @foreach (json_decode($openai->questions) ?? [] as $question)
                        @if ($question?->type == 'text')
                            <x-forms.input
                                id="{{ $question?->name }}"
                                size="lg"
                                label="{{ __($question?->question) }}"
                                type="{{ $question?->type }}"
                                name="{{ $question?->name }}"
                                placeholder="{{ __($question?->question) }}"
                                required
                            />
                        @elseif($question?->type == 'textarea')
                            <x-forms.input
                                id="{{ $question?->name }}"
                                size="lg"
                                label="{{ __($question?->question) }}"
                                name="{{ $question?->name }}"
                                type="textarea"
                                rows="8"
                                placeholder="{{ __($question?->question) }}"
                                required
                            />
                        @elseif($question?->type == 'select')
                            <x-forms.input
                                id="{{ $question?->name }}"
                                size="lg"
                                label="{{ __($question?->question) }}"
                                name="{{ $question?->name }}"
                                type="select"
                                required
                            >
								@foreach ($question?->selectListValues ?? [] as $input)
									<option value="{{ $input }}">
										{{ $input }}
									</option>
								@endforeach
                            </x-forms.input>
                        @elseif($question?->type == 'file')
                            <x-forms.input
                                class="{{ $openai->type === 'audio' ? 'absolute opacity-0 top-0 start-0 cursor-pointer w-full h-full z-3' : '' }}"
                                class:label="static text-center text-heading-foreground/40 text-3xs lg:px-10"
                                id="{{ $question?->name }}"
                                container-class="static"
                                size="lg"
                                label="{{ __($question?->question) }}"
                                name="{{ $question?->name }}"
                                type="file"
                                placeholder="{{ __($question?->question) }}"
                                required
                                x-ref="file"
                                @change="if ( $refs.fileName ) { $refs.fileName.innerText = $refs.file.files[0].name }"
                                @drop.prevent="if ( $refs.fileName ) { $refs.fileName.innerText = event.dataTransfer.files[0].name; } $refs.file.files = event.dataTransfer.files"
                            />
                        @endif
                    @endforeach

                    @if ($openai->type == 'text')
                        @if (setting('hide_output_length_option') != 1)
                            <x-forms.input
                                id="maximum_length"
                                size="lg"
                                label="{{ __('Maximum Length') }}"
                                name="maximum_length"
                                type="number"
                                placeholder="{{ __('Maximum character length of text') }}"
                                required
                                min="1"
                                step="1"
                            />
                        @endif

                        @if (setting('hide_creativity_option') != 1)
                            <x-forms.input
                                id="creativity"
                                size="lg"
                                label="{{ __('Creativity') }}"
                                name="creativity"
                                type="select"
                                required
                            >
                                @foreach ($creativity_levels as $creativity => $label)
                                    <option
                                        value="{{ $creativity }}"
                                        @selected($setting->openai_default_creativity == $creativity)
                                    >
                                        {{ __($label) }}
                                    </option>
                                @endforeach
                            </x-forms.input>
                        @endif

                        <x-forms.input
                            id="language"
                            size="lg"
                            label="{{ __('Language') }}"
                            name="language"
                            type="select"
                            required
                        >
                            @include('panel.user.openai.components.countries')
                        </x-forms.input>

                        @if (setting('hide_tone_of_voice_option') != 1)
                            <x-forms.input
                                id="tone_of_voice"
                                size="lg"
                                type="select"
                                label="{{ __('Tone of Voice') }}"
                                containerClass="w-full"
                                name="tone_of_voice"
                                required
                            >
                                @foreach ($voice_tones as $tone)
                                    <option
                                        value="{{ $tone }}"
                                        @selected($setting->openai_default_tone_of_voice == $tone)
                                    >
                                        {{ __($tone) }}
                                    </option>
                                @endforeach
                            </x-forms.input>
                            <x-forms.input
                                class:container="hidden w-full md:w-[48%]"
                                id="tone_of_voice_custom"
                                name="tone_of_voice_custom"
                                type="text"
                                label="{{ __('Enter custom tone') }}"
                                switcher
                            />
                        @endif

                        <x-forms.input
                            id="number_of_results"
                            size="lg"
                            type="number"
                            label="{{ __('Number of Results') }}"
                            name="number_of_results"
                            placeholder="{{ __('Maximum character length of text') }}"
                            required
                            min="1"
                            value="1"
                        />
                    @endif

                    <x-button
                        class="relative z-10 w-full"
                        id="openai_generator_button"
                        size="lg"
                        tag="button"
                        type="submit"
                        form="openai_generator_form"
                    >
                        {{ __('Generate') }}
                    </x-button>
                </form>
            </x-card>
        @endif
    </div>

    <x-card
        class="w-full [&_.tox-edit-area__iframe]:!bg-transparent"
        id="generator_sidebar_table"
        variant="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.variant', 'solid') }}"
        size="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.size', 'md') }}"
        roundness="{{ Theme::getSetting('defaultVariations.card.roundness', 'default') === 'default' ? 'none' : Theme::getSetting('defaultVariations.card.roundness', 'default') }}"
    >
        @include('panel.user.openai.components.generator_sidebar_table')
    </x-card>
</div>

@push('script')
    @if ($openai->type == 'code')
        <link
            rel="stylesheet"
            href="{{ custom_theme_url('/assets/libs/prism/prism.css') }}"
        >
        <script src="{{ custom_theme_url('/assets/libs/prism/prism.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', (event) => {
                "use strict";

                const codeLang = document.querySelector('#code_lang');
                const codePre = document.querySelector('#code-pre');
                const codeOutput = codePre?.querySelector('#code-output');

                if (codeOutput) {
                    let codeOutputText = codeOutput.textContent;
                    const codeBlocks = codeOutputText.match(/```[A-Za-z_]*\n[\s\S]+?```/g);
                    if (codeBlocks) {
                        codeBlocks.forEach((block) => {
                            const language = block.match(/```([A-Za-z_]*)/)[1];
                    const code = block.replace(/```[A-Za-z_]*\n/, '').replace(/```/, '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(
                        /"/g, '&quot;').replace(/'/g, '&#039;');
                    const wrappedCode = `<pre><code class="language-${language}">${code}</code></pre>`;
                    codeOutputText = codeOutputText.replace(block, wrappedCode);
                });
            }

            codePre.innerHTML = codeOutputText;

            codePre.querySelectorAll('pre').forEach(pre => {
                pre.classList.add(`language-${codeLang && codeLang.value !== '' ? codeLang.value : 'javascript'}`);
                    })

                    // saving for copy
                    window.codeRaw = codeOutput.innerText;

                    codePre.querySelectorAll('code').forEach(block => {
                        Prism.highlightElement(block);
                    });
                };
            });
            document.getElementById('tone_of_voice')?.addEventListener('change', function() {
                var customInput = document.getElementById('tone_of_voice_custom');
                if (this.value === 'other') {
                    customInput.parentNode.classList.remove('hidden');
                } else {
                    customInput.parentNode.classList.add('hidden');
                }
            });
        </script>
    @endif
@endpush
