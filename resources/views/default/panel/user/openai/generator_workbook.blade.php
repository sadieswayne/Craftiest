@php
    $creativity_levels = [
        '0.25' => 'Economic',
        '0.5' => 'Average',
        '0.75' => 'Good',
        '1' => 'Premium',
    ];

    $voice_tones = ['Professional', 'Funny', 'Casual', 'Excited', 'Witty', 'Sarcastic', 'Feminine', 'Masculine', 'Bold', 'Dramatic', 'Grumpy', 'Secretive', 'other'];

    $youtube_actions = [
        'blog' => __('Prepare a Blog Post'),
        'short' => __('Explain the Main Idea'),
        'list' => __('Create a List'),
        'tldr' => __('Create TLDR'),
        'prons_cons' => __('Prepare Pros and Cons'),
    ];
@endphp
@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __($openai->title))
@section('titlebar_subtitle')
    @if ($openai->type == 'code')
        {{ __('Generate high quality code in seconds.') }}
    @elseif(isset($openai->description))
        {{ __($openai->description) }}
    @endif
@endsection

@section('content')
    <div class="py-10">
        <div
            class="lqd-generator-wrap grid grid-flow-row gap-y-8 lg:grid-flow-col lg:[grid-template-columns:41%_59%]"
            data-generator-type="{{ $openai->type }}"
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
                        class="lqd-generator-form flex flex-wrap justify-between space-y-5"
                        id="openai_generator_form"
                        onsubmit="return sendOpenaiGeneratorForm();"
                    >
                        @if ($openai->type != 'code')
                            <div
                                class="flex w-full flex-col gap-5"
                                x-data="{ 'brandEnabled': false }"
                            >
                                <x-forms.input
                                    id="brand"
                                    type="checkbox"
                                    name="brand"
                                    label="{{ __('Include Your Brand') }}"
                                    @change="brandEnabled = $el.checked"
                                    switcher
                                />

                                <div
                                    class="hidden w-full flex-col gap-5"
                                    :class="{ 'hidden': !brandEnabled, 'flex': brandEnabled }"
                                >
                                    <x-forms.input
                                        id="company"
                                        size="lg"
                                        type="select"
                                        label="{{ __('Select Company') }}"
                                        name="company"
                                    >
                                        <x-slot:label-extra>
                                            <a
                                                class="inline-flex size-6 items-center justify-center rounded-lg bg-green-500/20 text-green-700 transition-all hover:scale-110 hover:bg-green-500 hover:text-green-100"
                                                href="{{  (route('dashboard.user.brand.create')) }}"
                                            >
                                                <x-tabler-plus class="size-4" />
                                            </a>
                                        </x-slot:label-extra>
                                        <option value="">
                                            {{ __('Select Company') }}
                                        </option>
                                        @foreach (auth()->user()->getCompanies() as $company)
                                            <option
                                                data-tone_of_voice="{{ $company->tone_of_voice }}"
                                                value="{{ $company->id }}"
                                            >
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </x-forms.input>

                                    <x-forms.input
                                        id="product"
                                        name="product"
                                        type="select"
                                        size="lg"
                                        label="{{ __('Select Product/Service') }}"
                                    >
                                        <option value="">{{ __('Select Product') }}</option>
                                    </x-forms.input>
                                </div>
                            </div>

                            <div
                                class="flex w-full flex-col gap-5"
                                x-data="{ 'bulkEnabled': false }"
                            >
                                <x-forms.input
                                    id="bulk"
                                    type="checkbox"
                                    name="bulk"
                                    label="{{ __('Generate Bulk Posts') }}"
                                    @change="bulkEnabled = $el.checked"
                                    switcher
                                />

                                <div
                                    class="hidden w-full flex-col gap-5"
                                    :class="{ 'hidden': !bulkEnabled, 'flex': bulkEnabled }"
                                >
                                    <x-forms.input
                                        id="number_of_results"
                                        size="lg"
                                        type="number"
                                        label="{{ __('Number of Results') }}"
                                        containerClass="w-full"
                                        name="number_of_results"
                                        min="1"
                                        value="1"
                                        placeholder="{{ __('Number of results') }}"
                                        required
                                    />
                                </div>
                            </div>
                        @endif
                        @foreach (json_decode($openai->questions) ?? [] as $question)
                            @php
                                $placeholder = isset($question->description) && !empty($question->description) ? __($question->description) : __($question->question);
                            @endphp
                            <x-forms.input
                                id="{{ $question->name }}"
                                required
                                size="lg"
                                containerClass="w-full"
                                label="{{ __($question->question) }}"
                                type="{{ $question->type === 'rss_feed' ? 'text' : $question->type }}"
                                name="{{ $question->name }}"
                                maxlength="{{ $setting->openai_max_input_length }}"
                                rows="{{ $question->type === 'textarea' ? 8 : null }}"
                                placeholder="{{ __($placeholder) }}"
                            >
                                @if ($question->type === 'select')
                                    @foreach ($question->selectListValues ?? [] as $input)
                                        <option value="{{ $input }}">
                                            {{ $input }}
                                        </option>
                                    @endforeach
                                @endif
                                @if ($question->type === 'rss_feed')
                                    <x-slot:action>
                                        <button
                                            class="fetch-rss rounded-e-inpu1t flex h-full items-center gap-2 px-3 text-2xs font-medium transition-colors hover:bg-secondary hover:text-secondary-foreground"
                                            type="button"
                                        >
                                            <x-tabler-refresh class="size-4" />
                                            {{ __('Fetch RSS') }}
                                        </button>
                                    </x-slot:action>
                                @endif
                            </x-forms.input>
                        @endforeach
                        @if ($openai->type == 'youtube')
                            <x-forms.input
                                id="youtube_action"
                                size="lg"
                                type="select"
                                label="{{ __('Action') }}"
                                containerClass="w-full"
                                name="youtube_action"
                                required
                            >
                                @foreach ($youtube_actions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </x-forms.input>
                        @endif
                        @switch($openai->type)
                            @case('youtube')
                            @case('text')

                            @case('rss')
                                <x-forms.input
                                    id="language"
                                    size="lg"
                                    type="select"
                                    label="{{ __('Language') }}"
                                    containerClass="w-full md:w-[48%]"
                                    name="language"
                                    required
                                >
                                    @include('panel.user.openai.components.countries')
                                </x-forms.input>

                                @if (setting('hide_output_length_option') != 1)
                                    <x-forms.input
                                        id="maximum_length"
                                        size="lg"
                                        type="number"
                                        label="{{ __('Maximum Length') }}"
                                        containerClass="w-full md:w-[48%]"
                                        name="maximum_length"
                                        max="{{ $setting->openai_max_output_length }}"
                                        value="{{ $setting->openai_max_output_length }}"
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
                                        type="select"
                                        label="{{ __('Creativity') }}"
                                        containerClass="w-full md:w-[48%]"
                                        name="creativity"
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

                                @if (setting('hide_tone_of_voice_option') != 1)
                                    <x-forms.input
                                        id="tone_of_voice"
                                        size="lg"
                                        type="select"
                                        label="{{ __('Tone of Voice') }}"
                                        containerClass="w-full md:w-[48%]"
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
                            @break

                            @default
                        @endswitch
                        @if ($models->count() && setting('select_model_option', '0') == '1')
                            <x-forms.input
                                id="chatbot_front_model"
                                type="select"
                                size="lg"
                                name="chatbot_front_model"
                                label="{{ __('Select model') }}"
                            >
                                <option value="">
                                    {{ __('Select model') }}
                                </option>
                                @foreach ($models as $model)
                                    <option value="{{ $model->key }}">
                                        {{ $model->selected_title }}
                                    </option>
                                @endforeach
                            </x-forms.input>
                        @endif
                        @if (setting('open_router_status') == 1)
                            @includeFirst(['open-router::open-router-select', 'vendor.empty'])
                        @endif
                        <x-button
                            class="mt-3 w-full"
                            id="openai_generator_button"
                            tag="button"
                            type="submit"
                            size="lg"
                        >
                            <span class="hidden group-[.lqd-form-submitting]:inline-flex">{{ __('Please wait...') }}</span>
                            <span class="group-[.lqd-form-submitting]:hidden">{{ __('Generate') }}</span>
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

                <div class="lqd-generator-actions flex w-full flex-wrap items-center gap-3 text-2xs">
                    <div class="flex grow">
                        @include('panel.user.openai.components.workbook-actions', [
                            'type' => $openai->type,
                            'title' => $openai->title,
                            'slug' => $openai->slug,
                            'output' => $openai->output,
                            'is_generated_doc' => true,
                        ])
                    </div>
                    <div
                        class="hidden justify-end"
                        id="updateDiv"
                    >
                        <x-button
                            id="workbook_resave"
                            size="sm"
                            variant="ghost-shadow"
                            href="javascript:void(0)"
                        >
                            <x-tabler-refresh
                                class="size-5"
                                stroke-width="1"
                            />
                            {{ __('Save') }}
                        </x-button>
                    </div>
                </div>
                <div class="lqd-generator-form-wrap mt-4 min-h-full w-full border-t pt-6">
                    {{-- <div
                        class="mb-5 me-5 hidden"
                        id="savedDiv"
                    >
                        <a
                            class="flex items-center gap-1.5 rounded-md bg-green-600/10 px-2.5 py-1 text-[12px] font-medium leading-none text-green-700 transition-all hover:scale-105 hover:shadow-lg hover:shadow-green-600/5 dark:text-green-400"
                            href="{{ route('dashboard.user.openai.documents.all') }}"
                        >
                            <x-tabler-folder-check class="size-5" />
                            {{ __('Saved to') }}
                            <span class="underline">{{ __('Documents') }}</span>
                        </a>
                    </div> --}}
                    @if ($openai->type == 'code')
                        <div
                            class="line-numbers min-h-full resize [direction:ltr] [&_kbd]:inline-flex [&_kbd]:rounded [&_kbd]:bg-primary/10 [&_kbd]:px-1 [&_kbd]:py-0.5 [&_kbd]:font-semibold [&_kbd]:text-primary [&_pre[class*=language]]:my-4 [&_pre[class*=language]]:rounded"
                            id="code-pre"
                        >
                            <div
                                class="prose dark:prose-invert"
                                id="code-output"
                            >...</div>
                        </div>
                    @else
                        <form class="workbook-form flex flex-col gap-4">
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
                    @endif
                </div>
            </x-card>
        </div>
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
    <input
        id="_message_no"
        type="hidden"
        name="_message_no"
    >

    <input
        id="_prompt"
        type="hidden"
        name="_prompt"
    >
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
        @if (setting('default_ai_engine', 'openai') == \App\Domains\Engine\Enums\EngineEnum::ANTHROPIC->value)
            const stream_type = 'backend';
        @else
            const stream_type = '{!! $settings_two->openai_default_stream_server !!}';
        @endif
        const openai_model = '{{ $setting->openai_default_model }}';
        const default_ai_engine = '{{ setting('default_ai_engine', 'openai') }}'
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

    @if ($openai->type == 'code')
        <link
            rel="stylesheet"
            href="{{ custom_theme_url('/assets/libs/prism/prism.css') }}"
        >
        <script src="{{ custom_theme_url('/assets/libs/prism/prism.js') }}"></script>
    @endif
    <script>
        var generated_document_slug = '';

        function sendOpenaiGeneratorForm(ev) {
            @if ($openai->type == 'youtube' && $app_is_demo)
                toastr.info("{{ __('This feature is restricted in the demo version.') }}");
                return false;
            @endif

            $('#savedDiv').addClass('hidden');
            $('#updateDiv').addClass('hidden');
            tinyMCE?.activeEditor?.setContent('');
            ev?.preventDefault();
            ev?.stopPropagation();
            const submitBtn = document.getElementById("openai_generator_button");
            const editArea = document.querySelector('.tox-edit-area');
            const typingTemplate = document.querySelector('#typing-template').content.cloneNode(true);
            const typingEl = typingTemplate.firstElementChild;
            const workbook_regenerate = document.querySelector('#workbook_regenerate');
            const chatbotFrontModelElement = document.getElementById('chatbot_front_model');
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
            // if brand checked then include company and product in request
            if (document.getElementById('brand')?.checked) {
                formData.append('company', document.getElementById('company').value);
                formData.append('product', document.getElementById('product').value);
            }
            formData.append('post_type', '{{ $openai->slug }}');
            formData.append('openai_id', '{{ $openai->id }}');

            if (chatbotFrontModelElement) {
                formData.append('chatbot_front_model', chatbotFrontModelElement.value);
            }

            @if ($openai->type == 'text' || $openai->type == 'rss' || $openai->type == 'youtube')
                formData.append('maximum_length', $("#maximum_length").val());
                formData.append('number_of_results', $("#number_of_results").val());
                formData.append('creativity', $("#creativity").val());
                formData.append('tone_of_voice', $("#tone_of_voice").val());
                formData.append('language', $("#language").val());
                formData.append('tone_of_voice_custom', $("#tone_of_voice_custom").val());
            @endif

            @if ($openai->type == 'youtube')

                formData.append('youtube_action', $("#youtube_action").val());
            @endif

            @foreach (json_decode($openai->questions) ?? [] as $question)
                formData.append('{{ $question->name }}', $("{{ '#' . $question->name }}").val());
            @endforeach

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
                    $("#_message_no").val(data.message_id);
                    $("#_prompt").val(data.inputPrompt);

                    @if ($openai->type == 'code')
                        toastr.success("{{ __('Generated Successfully!') }}");
                        $("#workbook_textarea").html(data.html);
                        const codeLang = document.querySelector('#code_lang');
                        const codePre = document.querySelector('#code-pre');
                        const codeOutput = codePre?.querySelector('#code-output');

                        if (codeOutput) {
                            // saving for copy
                            window.codeRaw = codeOutput.innerText;

                            codeOutput.innerHTML = lqdFormatString(codeOutput.textContent);
                        };
                        endResponse(submitBtn, workbook_regenerate, typingEl);
                    @else
                        const typingEl = document.querySelector('.tox-edit-area > .lqd-typing');
                        const message_no = data.message_id;
                        const creativity = data.creativity;
                        const maximum_length = parseInt(data.maximum_length);
                        const number_of_results = data.number_of_results;
                        const prompt = data.inputPrompt;
                        const openai_id = '{{ $openai->id }}';
                        const open_router_model = $("#open_router_model").val();
                        generate(message_no, creativity, maximum_length, number_of_results, prompt, openai_id, open_router_model);
                    @endif
                },
                error: function(data) {
                    if (data.responseJSON.errors) {
                        $.each(data.responseJSON.errors, function(index, value) {
                            toastr.error(value);
                        });
                    } else if (data.responseJSON.message) {
                        toastr.error(data.responseJSON.message);
                    }
                    endResponse(submitBtn, workbook_regenerate, typingEl);
                }
            }).done(function() {
                setTimeout(function() {
                    $('#savedDiv').removeClass('hidden');
                    $('#updateDiv').removeClass('hidden');
                }, 3000);
            });
            return false;
        }

        const workbook_resave = document.getElementById("workbook_resave");
        workbook_resave?.addEventListener("click", function() {
            const editor = tinyMCE.activeEditor;
            if (editor) {
                const title = document.getElementById("workbook_title").value;
                const content = editor.getContent();
                const message_no = $("#_message_no").val();
                const prompt = $("#_prompt").val();
                saveResponse(prompt, content, message_no, title, true);
                toastr.success('Document saved successfully!');
            }
        });


        const deleteButton = document.getElementById("workbook_delete");
        deleteButton?.addEventListener("click", clearWorkbookContent);

        function clearWorkbookContent() {
            const editor = tinyMCE.activeEditor;
            if (editor) {
                editor.setContent("");
            }
        }
    </script>

    @if ($openai->type == 'rss')
        <script>
            $(document).on("click", ".fetch-rss", function(e) {
                "use strict";

                if (!$('#rss_feed').val()) {
                    toastr.error(@json(__('Enter the RSS URL!')));
                    return false;
                }

                var formData = new FormData();
                formData.append('url', $('#rss_feed').val());


                $.ajax({
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    url: "/rss/fetch",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.fetch-rss svg').addClass('animate-spin');
                    },
                    success: function(data) {
                        $('.fetch-rss svg').removeClass('animate-spin');
                        $('#title').empty();
                        $('#title').append(data);
                        toastr.success(@json(__('RSS Fetched Successifuly!')));
                    },
                    error: function(data) {
                        $('.fetch-rss svg').removeClass('animate-spin');
                        toastr.error(data.responseJSON);
                    }
                });

            });
        </script>
    @endif

    <script>
        $('#company').on('change', function() {
            var brand_id = $(this).val();
            if (brand_id == '') {
                $('#product').empty();
                $('#product').append('<option value="">Select Product</option>');
                $('#tone_of_voice option').prop('selected', false);
            } else {
                $.ajax({
                    url: '/dashboard/user/brand/get-products/' + brand_id,
                    type: 'get',
                    success: function(response) {
                        $('#product').empty();
                        if (response.length == 0) {
                            $('#product').append('<option value="">Select Product</option>');
                        } else {
                            $.each(response, function(index, value) {
                                $('#product').append('<option value="' + value.id + '">' + value
                                    .name +
                                    '</option>');
                            });
                        }
                        $('#tone_of_voice').val($('#company :selected').attr('data-tone_of_voice'));
                    }
                });
            }

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

@endpush
