<div class="lqd-generator-v2-editor">
    <div class="lqd-generator-editor-actions fixed inset-x-0 top-0 z-50">
        @include('panel.user.generator.components.editor-actions-top-bar')
    </div>

    <div class="rela`tive container">
        <div class="mx-auto lg:w-2/3">
            <form
                class="lqd-tinymce-toolbar-fixed workbook-form pt-[calc(var(--editor-tb-h)+var(--editor-bb-h)+3rem)] max-md:group-[&.lqd-generator-sidebar-collapsed]/generator:ps-8"
                x-data="{}"
                @submit.prevent
            >
                <textarea
                    class="tinymce w-full border-none bg-transparent"
                    id="default"
                    rows="25"
                >

                    @if (isset($workbook))
{!! $workbook->output !!}
@endif

                </textarea>
            </form>
        </div>
        <form
            class="group/form invisible fixed bottom-8 left-1/2 flex w-[min(95vw,680px)] -translate-x-1/2 translate-y-4 items-center justify-between rounded-full p-1 opacity-0 transition-all group-[&.lqd-generator-sidebar-collapsed]/generator:visible group-[&.lqd-generator-sidebar-collapsed]/generator:translate-y-0 group-[&.lqd-generator-sidebar-collapsed]/generator:opacity-100 max-lg:z-40"
            data-ai="prompt"
            action="#"
        >
            <div class="absolute inset-0 overflow-hidden rounded-[inherit]">
                <div class="animate-spin [animation-play-state:paused] group-[&.is-loading]/form:[animation-play-state:running]">
                    <div
                        class="absolute start-1/2 top-1/2 aspect-square min-h-[110%] min-w-[110%] -translate-x-1/2 -translate-y-1/2 rounded-[inherit] bg-gradient-to-r from-[#9C76F9] to-[#BBDAFF]">
                    </div>
                </div>
            </div>
            <div class="relative flex w-full items-center justify-between rounded-full bg-background pe-3">
                {{-- blade-formatter-disable --}}
				<svg class="absolute start-7 top-1/2 -translate-y-1/2 pointer-events-none" width="20" height="20" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" > <path fill-rule="evenodd" clip-rule="evenodd" d="M16.1681 6.15216L14.7761 6.43416V6.43616C14.1057 6.57221 13.4902 6.90274 13.0064 7.38647C12.5227 7.87021 12.1922 8.48572 12.0561 9.15617L11.7741 10.5482C11.7443 10.6852 11.6686 10.8079 11.5594 10.8958C11.4503 10.9838 11.3143 11.0318 11.1741 11.0318C11.0339 11.0318 10.8979 10.9838 10.7888 10.8958C10.6796 10.8079 10.6039 10.6852 10.5741 10.5482L10.2921 9.15617C10.1563 8.48561 9.82586 7.86997 9.34209 7.38619C8.85831 6.90241 8.24266 6.57197 7.57211 6.43616L6.18011 6.15416C6.0413 6.12574 5.91656 6.05026 5.82698 5.94048C5.7374 5.8307 5.68848 5.69336 5.68848 5.55166C5.68848 5.40997 5.7374 5.27263 5.82698 5.16285C5.91656 5.05307 6.0413 4.97759 6.18011 4.94916L7.57211 4.66716C8.24261 4.53124 8.85819 4.20076 9.34195 3.717C9.8257 3.23324 10.1562 2.61766 10.2921 1.94716L10.5741 0.555164C10.6039 0.418164 10.6796 0.295476 10.7888 0.207494C10.8979 0.119512 11.0339 0.0715332 11.1741 0.0715332C11.3143 0.0715332 11.4503 0.119512 11.5594 0.207494C11.6686 0.295476 11.7443 0.418164 11.7741 0.555164L12.0561 1.94716C12.1922 2.61761 12.5227 3.23312 13.0064 3.71686C13.4902 4.20059 14.1057 4.53112 14.7761 4.66716L16.1681 4.94716C16.3069 4.97559 16.4317 5.05107 16.5212 5.16085C16.6108 5.27063 16.6597 5.40797 16.6597 5.54966C16.6597 5.69136 16.6108 5.8287 16.5212 5.93848C16.4317 6.04826 16.3069 6.12374 16.1681 6.15216ZM5.98931 13.2052L5.61131 13.2822C5.14508 13.3767 4.71703 13.6055 4.38056 13.9418C4.04409 14.2781 3.81411 14.706 3.71931 15.1722L3.64231 15.5502C3.62171 15.6567 3.56468 15.7527 3.48102 15.8217C3.39735 15.8907 3.29227 15.9285 3.18381 15.9285C3.07534 15.9285 2.97026 15.8907 2.88659 15.8217C2.80293 15.7527 2.74591 15.6567 2.72531 15.5502L2.6483 15.1722C2.55362 14.7059 2.32368 14.2779 1.98719 13.9416C1.6507 13.6053 1.22258 13.3756 0.756305 13.2812L0.378305 13.2042C0.271814 13.1836 0.175815 13.1265 0.106785 13.0429C0.037755 12.9592 0 12.8541 0 12.7457C0 12.6372 0.037755 12.5321 0.106785 12.4485C0.175815 12.3648 0.271814 12.3078 0.378305 12.2872L0.756305 12.2102C1.22271 12.1157 1.65093 11.8858 1.98743 11.5493C2.32393 11.2128 2.5538 10.7846 2.6483 10.3182L2.72531 9.94016C2.74591 9.83367 2.80293 9.73767 2.88659 9.66864C2.97026 9.59961 3.07534 9.56186 3.18381 9.56186C3.29227 9.56186 3.39735 9.59961 3.48102 9.66864C3.56468 9.73767 3.62171 9.83367 3.64231 9.94016L3.71931 10.3182C3.81376 10.7847 4.04359 11.2131 4.38008 11.5497C4.71658 11.8864 5.14482 12.1165 5.61131 12.2112L5.98931 12.2882C6.0958 12.3088 6.1918 12.3658 6.26083 12.4495C6.32985 12.5331 6.36761 12.6382 6.36761 12.7467C6.36761 12.8551 6.32985 12.9602 6.26083 13.0439C6.1918 13.1275 6.0958 13.1846 5.98931 13.2052Z" fill="url(#paint0_linear_3314_1636)" ></path> <defs> <linearGradient id="paint0_linear_3314_1636" x1="1.03221e-07" y1="3.30635" x2="13.3702" y2="15.6959" gradientUnits="userSpaceOnUse" > <stop stop-color="#82E2F4"></stop> <stop offset="0.502" stop-color="#8A8AED" ></stop> <stop offset="1" stop-color="#6977DE" ></stop> </linearGradient> </defs> </svg>
				{{-- blade-formatter-enable --}}
                <input
                    class="h-10 grow rounded-full bg-transparent pe-3 ps-16 text-xs text-heading-foreground focus:border-transparent focus:outline-none focus:ring-0"
                    id="ai_prompt_id"
                    type="text"
                    placeholder="{{ __('Keep writing the next paragraph...') }}"
                >
                <button
                    class="relative ms-2 flex size-6 items-center justify-center rounded-full bg-heading-foreground/5 p-0 text-heading-foreground transition-all hover:bg-heading-foreground hover:text-heading-background disabled:pointer-events-none"
                    type="submit"
                >
                    {{-- blade-formatter-disable --}}
					<svg class="rtl:rotate-180 transition-all group-[&.is-loading]/form:scale-50 group-[&.is-loading]/form:opacity-0" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" > <path stroke="none" d="M0 0h24v24H0z" fill="none" /> <path d="M5 12l14 0" /> <path d="M13 18l6 -6" /> <path d="M13 6l6 6" /> </svg>
					{{-- blade-formatter-enable --}}
                    <span
                        class="absolute inset-0 grid scale-150 grid-cols-1 grid-rows-1 place-items-center opacity-0 transition-all group-[&.is-loading]/form:scale-100 group-[&.is-loading]/form:opacity-100"
                    >
                        <span class="col-start-1 col-end-1 row-start-1 row-end-1 inline-block size-1.5 rounded-full bg-current"></span>
                        <span
                            class="col-start-1 col-end-1 row-start-1 row-end-1 inline-block size-2 animate-ping rounded-full bg-current [animation-play-state:paused] group-[&.is-loading]/form:[animation-play-state:running]"
                        ></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<input
    id="_message_no"
    type="hidden"
    name="_message_no"
    value="{{ isset($workbook) ? $workbook->id : '' }}"
>

<input
    id="_prompt"
    type="hidden"
    name="_prompt"
>

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
        const lang_with_flags = @json($lang_with_flags);
    </script>
    <script src="{{ custom_theme_url('/assets/libs/beautify-html.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ace.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ext-language_tools.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/markdown-it.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/turndown.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/html2pdf/html2pdf.bundle.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/tinymce-theme-handler.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/format-string.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/panel/openai_generator_workbook.js?v=' . time()) }}"></script>

    <link
        rel="stylesheet"
        href="{{ custom_theme_url('/assets/libs/prism/prism.css') }}"
    >
    <script src="{{ custom_theme_url('/assets/libs/prism/prism.js') }}"></script>

    <script>
        function saveDocTitle(title) {
            let message_no = $("#_message_no").val();
            // if (!message_no) {
            //     toastr.error("{{ __('You haven\'t created any content') }}")
            // }
            var formData = new FormData();
            formData.append('title', title);
            formData.append('message_id', message_no);
            $.ajax({
                url: '/dashboard/user/openai/message/title_save',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(data) {
                toastr.success(data.message);
            }).fail(function(data) {
                toastr.error(data.responseJSON.message);
            });
        }

        $('#document_title').on('keypress', function(ev) {
            const input = $(this);
            if (ev.code === 'Enter') {
                ev.preventDefault();
                saveDocTitle(input.val());
                input.blur();
            }
        });

        $(document).on('change', '#document_title', function() {
            saveDocTitle($(this).val());
        });


        $(document).on('submit', '[data-ai="prompt"]', function(ev) {
            ev.preventDefault();
            const form = ev.currentTarget;
            const submitBtn = form.querySelector('button[type="submit"]');
            const ai_prompt_id = document.querySelector('#ai_prompt_id');
            let content = tinyMCE.activeEditor.getContent();

            if (tinyMCE.activeEditor.selection.getContent().trim().length > 0) {
                content = tinyMCE.activeEditor.selection.getContent();
            }

            if (content.trim().length === 0) {
                toastr.warning("Please write something");
                return;
            }

            Alpine.store('appLoadingIndicator').show();
            form.classList.add('is-loading');
            submitBtn.disabled = true;

            let formData = new FormData();

            formData.append('prompt', $('#ai_prompt_id').val() ||
                'Continue writing from the end of the content. Maintain the same tone, style, and subject matter as the preceding text. Ensure that the continuation flows naturally and stays true to the original content. Expand on the ideas presented and provide a coherent and engaging continuation.'
            );

            formData.append('content', content);

            $.ajax({
                type: "post",
                url: "/dashboard/user/openai/update-writing",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    tinyMCE.activeEditor.selection.setContent(data.result);

                    const timeout = setTimeout(() => {
                        // moving the cursor to the end
                        tinymce.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
                        tinymce.activeEditor.selection.collapse(false);
                        tinymce.activeEditor.focus();
                        tinymce.activeEditor.insertContent('<p></p>');

                        clearTimeout(timeout);
                    }, 100);

                    Alpine.store('appLoadingIndicator').hide();
                    form.classList.remove('is-loading');
                    submitBtn.disabled = false;
                },
                error: function(data) {
                    Alpine.store('appLoadingIndicator').hide();
                    form.classList.remove('is-loading');
                    submitBtn.disabled = false;
                }
            });
        })

        $(document).on('click', '.web_hook_save', function() {
            const message_no = $("#_message_no").val();
            const prompt = $("#_prompt").val();
            const title = $('#document_title').val();
            let result = tinyMCE.activeEditor.getContent();
            saveResponse(prompt, result, message_no, title);
            toastr.success('Document saved successfully!');
        });

        $(document).on('click', '#openai_generator_button', function() {
            let openai_type = $('#openai_type').val();
            let openai_slug = $('#openai_slug').val();
            let openai_questions = JSON.parse($('#openai_questions').val());
            let openai_id = $('#openai_id').val();
            let document_title = $('#document_title').val();
            const ai_prompt_id = document.querySelector('#ai_prompt_id');

            tinyMCE?.activeEditor?.setContent('');

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
            // if brand checked then include company and product in request
            if (document.getElementById('brand')?.checked) {
                formData.append('company', document.getElementById('company').value);
                formData.append('product', document.getElementById('product').value);
            }
            formData.append('post_type', openai_slug);
            formData.append('openai_id', openai_id);
            formData.append('title', document_title);

            if (openai_type === 'text') {
                formData.append('maximum_length', $("#maximum_length").val());
                formData.append('number_of_results', $("#number_of_results").val());
                formData.append('creativity', $("#creativity").val());
                formData.append('tone_of_voice', $("#tone_of_voice").val());
                formData.append('language', $("#language").val());
                formData.append('tone_of_voice_custom', $("#tone_of_voice_custom").val());
            }

            if (openai_type === 'youtube') {
                formData.append('language', $("#language").val());
                formData.append('youtube_action', $("#youtube_action").val());
            }

            if (openai_questions.length) {
                for (const property in openai_questions) {
                    if (openai_questions[property]?.name) {
                        var element = $('#' + openai_questions[property].name).val();
                        formData.append(openai_questions[property].name, element);
                    }
                }
            }

            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                url: "/dashboard/user/openai/generate",
                data: formData,
                contentType: false,
                processData: false,
                success: async function(data) {

                    $("#_message_no").val(data.message_id);
                    $("#_prompt").val(data.inputPrompt);

                    // Collapsing the generator sidebar
                    Alpine.$data(document.querySelector('.lqd-generator-v2')).toggleSideNavCollapse(
                        'collapse');

                    if (openai_type == 'code') {
                        // because data changing in the dom can't cache codeOutput
                        // const codeOutput = $("#code-output");
                        toastr.success("{{ __('Generated Successfully!') }}");
                        // if ( $("#code-output").length ) {
                        $("#workbook_textarea").html(data.html);
                        const codeLang = document.querySelector('#code_lang');
                        const codePre = document.querySelector('#code-pre');
                        const codeOutput = codePre?.querySelector('#code-output');

                        if (codeOutput) {
                            // saving for copy
                            window.codeRaw = codeOutput.innerText;

                            codeOutput.innerHTML = lqdFormatString(codeOutput.textContent);
                        };
                        // } else {
                        //     tinymce.activeEditor.destroy();
                        //     $("#workbook_textarea").html(data.html);
                        //     getResult();
                        // }
                        submitBtn.classList.remove('lqd-form-submitting');
                        Alpine.store('appLoadingIndicator').hide();
                        document.querySelector('#workbook_regenerate')?.classList?.remove('hidden');
                        submitBtn.disabled = false;
                    } else {
                        const typingEl = document.querySelector('.tox-edit-area > .lqd-typing');
                    }
                    const message_no = data.message_id;
                    const creativity = data.creativity;
                    const maximum_length = parseInt(data.maximum_length);
                    const number_of_results = data.number_of_results;
                    const prompt = data.inputPrompt;

                    generate(message_no, creativity, maximum_length, number_of_results, prompt, openai_id);

                    const timeout = setTimeout(function() {
                        $('#savedDiv').removeClass('hidden');

                        clearTimeout(timeout);
                    }, 1000);
                },
                error: function(data) {
                    if (data.responseJSON.errors) {
                        $.each(data.responseJSON.errors, function(index, value) {
                            toastr.error(value);
                        });
                    } else if (data.responseJSON.message) {
                        toastr.error(data.responseJSON.message);
                    }

                    submitBtn.classList.remove('lqd-form-submitting');

                    Alpine.store('appLoadingIndicator').hide();

                    document.querySelector('#workbook_regenerate')?.classList?.add('hidden');

                    submitBtn.disabled = false;
                }
            });
        });
    </script>
@endpush
