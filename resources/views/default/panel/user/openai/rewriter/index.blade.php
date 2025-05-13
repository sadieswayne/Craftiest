@php
    $voice_tones = ['Professional', 'Funny', 'Casual', 'Excited', 'Witty', 'Sarcastic', 'Feminine', 'Masculine', 'Bold', 'Dramatic', 'Grumpy', 'Secretive', 'other'];
@endphp

@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('AI ReWriter'))
@section('titlebar_subtitle', __('Effortlessly reshape and elevate your pre-existing content with a single click.'))

@section('content')
    <div class="py-10">
        <div
            class="lqd-generator-wrap grid grid-flow-row gap-y-8 lg:grid-flow-col lg:[grid-template-columns:41%_59%]"
            data-generator-type="rewrite"
        >
            <div class="flex w-full flex-col gap-6 lg:pe-14">
                <x-card class="lqd-generator-remaining-credits">
                    <h5 class="mb-3 text-xs font-normal">
                        {{ __('Remaining Credits') }}
                    </h5>

                    <x-credit-list />
                </x-card>

                <x-card
                    class="lqd-generator-options-card"
                    variant="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.variant', 'solid') }}"
                    size="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.size', 'md') }}"
                    roundness="{{ Theme::getSetting('defaultVariations.card.roundness', 'default') === 'default' ? 'none' : Theme::getSetting('defaultVariations.card.roundness', 'default') }}"
                >
                    <form
                        class="lqd-generator-form flex flex-col gap-y-5"
                        id="rewrite_content_form"
                        onsubmit="return sendOpenaiGeneratorForm();"
                        enctype="multipart/form-data"
                    >
                        <x-forms.input
                            id="content_rewrite"
                            size="lg"
                            type="textarea"
                            label="{{ __('Description') }}"
                            name="content_rewrite"
                            rows="10"
                            required
                        />

                        @if (setting('hide_tone_of_voice_option') != 1)
                            <x-forms.input
                                id="rewrite_mode"
                                size="lg"
                                type="select"
                                label="{{ __('Mode') }}"
                                name="rewrite_mode"
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
                            id="language"
                            size="lg"
                            type="select"
                            label="{{ __('Output Language') }}"
                            name="language"
                            required
                        >
                            @include('panel.user.openai.components.countries')
                        </x-forms.input>

                        <x-button
                            class="mt-2 w-full"
                            id="openai_generator_button"
                            tag="button"
                            size="lg"
                            type="submit"
                        >
                            {{ __('Generate') }}
                        </x-button>
                    </form>
                </x-card>
            </div>

            <x-card
                id="workbook_textarea"
                @class([
                    'w-full [&_.tox-edit-area__iframe]:!bg-transparent',
                    'lg:border-s lg:ps-16' =>
                        Theme::getSetting('defaultVariations.card.variant', 'outline') ===
                        'outline',
                ])
                variant="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.variant', 'solid') }}"
                size="{{ Theme::getSetting('defaultVariations.card.variant', 'outline') === 'outline' ? 'none' : Theme::getSetting('defaultVariations.card.size', 'md') }}"
                roundness="{{ Theme::getSetting('defaultVariations.card.roundness', 'default') === 'default' ? 'none' : Theme::getSetting('defaultVariations.card.roundness', 'default') }}"
            >
                <div class="flex flex-wrap items-center justify-between text-[13px]">
                    <div class="lqd-generator-actions flex w-full flex-wrap items-center gap-3">
                        <button
                            class="lqd-regenerate-btn flex items-center gap-2 border-none shadow-none"
                            id="btn_regenerate"
                            type="submit"
                            form="rewrite_content_form"
                        >
                            <x-tabler-arrows-right-left class="size-4" />
                            {{ __('Regenerate') }}
                        </button>
                        @include('panel.user.openai.components.workbook-actions', [
                            'type' => 'text',
                            'title' => __('AI ReWriter'),
                            'is_generated_doc' => true,
                        ])
                        {{-- <div
                            class="hidden items-end text-end"
                            id="savedDiv"
                        >
                            <a
                                class="text-nowrap flex items-center gap-1.5 rounded-md bg-green-600/10 px-2.5 py-1 text-[12px] font-medium leading-none text-green-700 transition-all hover:scale-105 hover:shadow-lg hover:shadow-green-600/5 dark:text-green-400"
                                href="{{ route('dashboard.user.openai.documents.all') }}"
                            >
                                <x-tabler-folder-check class="size-5" />
                                {{ __('Saved to') }}
                                <span class="underline">{{ __('Drafts') }}</span>
                            </a>
                        </div> --}}
                    </div>
                    <div class="lqd-generator-form-wrap mt-4 min-h-full w-full border-t pt-6">
                        <form class="workbook-form flex flex-col gap-4 [&_.tox-editor-header]:!shadow-none">
                            <x-forms.input
                                class="border-transparent px-0 font-serif text-2xl"
                                id="workbook_title"
                                placeholder="{{ __('Untitled Document...') }}"
                            />
                            <x-forms.input
                                class="tinymce border-0 font-body"
                                id="default"
                                type="textarea"
                                rows="25"
                            />
                        </form>
                    </div>
                </div>
            </x-card>
        </div>
        <input
            id="guest_id"
            type="hidden"
            value="{{ $apiUrl }}"
        >
        <input
            id="guest_event_id"
            type="hidden"
            value="{{ $apikeyPart1 }}"
        >
        <input
            id="guest_look_id"
            type="hidden"
            value="{{ $apikeyPart2 }}"
        >
        <input
            id="guest_product_id"
            type="hidden"
            value="{{ $apikeyPart3 }}"
        >
    </div>
@endsection

@php
    $lang_with_flags = [];
    foreach (LaravelLocalization::getSupportedLocales() as $lang => $properties) {
        $lang_with_flags[] = [
            'lang' => $lang,
            'name' => $properties['native'],
            'flag' => country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1)),
        ];
    }
@endphp
@push('script')
    <script>
        const lang_with_flags = @json($lang_with_flags);
    </script>
    <script src="{{ custom_theme_url('/assets/libs/beautify-html.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ace.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ext-language_tools.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/markdown-it.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/turndown.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/tinymce-theme-handler.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/format-string.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/openai_generator_workbook.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/wavesurfer/wavesurfer.js') }}"></script>
    <script>
        const stream_type = '{!! $settings_two->openai_default_stream_server !!}';
        const openai_model = '{{ $setting->openai_default_model }}';

        function sendOpenaiGeneratorForm(ev) {
            "use strict";
            $('#savedDiv').addClass('hidden');

            tinyMCE?.activeEditor?.setContent('');

            ev?.preventDefault();
            ev?.stopPropagation();
            const submitBtn = document.getElementById("openai_generator_button");
            const editArea = document.querySelector('.tox-edit-area');
            const typingTemplate = document.querySelector('#typing-template').content.cloneNode(true);
            const typingEl = typingTemplate.firstElementChild;
            Alpine.store('appLoadingIndicator').show();
            submitBtn.classList.add('lqd-form-submitting');
            submitBtn.disabled = true;

            if (editArea) {
                if (!editArea.querySelector('.lqd-typing')) {
                    editArea.appendChild(typingEl);
                } else {
                    editArea.querySelector('.lqd-typing')?.classList?.remove('lqd-is-hidden');
                }
            }

            var formData = new FormData();
            formData.append('post_type', 'ai_rewriter');
            formData.append('content_rewrite', $('#content_rewrite').val());

            formData.append('rewrite_mode', $("#rewrite_mode").val());
            formData.append('language', $("#language").val());

            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                url: "/dashboard/user/openai/generate",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    const typingEl = document.querySelector('.tox-edit-area > .lqd-typing');

                    const message_no = data.message_id;
                    const creativity = data.creativity;
                    const maximum_length = parseInt(data.maximum_length);
                    const number_of_results = data.number_of_results;
                    const prompt = data.inputPrompt;
                    const openai_id = '1';
                    generate(message_no, creativity, maximum_length, number_of_results, prompt, openai_id);
                    setTimeout(function() {
                        $('#savedDiv').removeClass('hidden');
                    }, 1000);
                },
                error: function(data) {
                    if (data.responseJSON.errors) {
                        toastr.error(data.responseJSON.errors);
                    } else if (data.responseJSON.message) {
                        toastr.error(data.responseJSON.message);
                    }
                    submitBtn.classList.remove('lqd-form-submitting');
                    Alpine.store('appLoadingIndicator').hide();
                    document.querySelector('#workbook_regenerate')?.classList?.add('hidden');
                    submitBtn.disabled = false;
                    const editArea = document.querySelector('.tox-edit-area');
                    editArea.querySelector('.lqd-typing')?.classList?.add('lqd-is-hidden');
                }
            });
            return false;
        };

        const deleteButton = document.getElementById("workbook_delete");
        deleteButton?.addEventListener("click", clearWorkbookContent);

        function clearWorkbookContent() {
            const editor = tinyMCE.activeEditor;
            if (editor) {
                editor.setContent("");
            }
        }

        document.getElementById('tone_of_voice')?.addEventListener('change', function() {
            var customInput = document.getElementById('tone_of_voice_custom');
            if (this.value === 'other') {
                customInput.parentNode.classList.remove('hidden');
            } else {
                customInput.parentNode.classList.add('hidden');
            }
        });
    </script>
@endpush
