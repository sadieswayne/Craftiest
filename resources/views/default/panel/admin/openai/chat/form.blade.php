@extends('panel.layout.settings')
@section('title', __('Add or Edit Chat Template'))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-10"
        id="custom_template_form"
        action="{{route("dashboard.admin.openai.chat.save")}}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        <div class="flex flex-col gap-5">
            <x-form-step
                step="1"
                label="{{ __('Template') }}"
            />
            <input hidden="" name="template_id" value="{{$template ? $template->id : null}}">
            <x-forms.input
                id="name"
                name="name"
                label="{{ __('Template Name') }}"
                size="lg"
                placeholder="{{ __('Template Name') }}"
                value="{{ $template != null ? $template->name : null }}"
                tooltip="{{ __('Pick a name for the template.') }}"
            />

            <x-forms.input
                id="chat_category"
                name="chat_category"
                label="{{ __('Category') }}"
                size="lg"
                placeholder="{{ __('Category') }}"
                tooltip="{{ __('Pick a category for the template.') }}"
                type="select"
            >
                @if ($template != null)
                    <option
                        value=""
                        @selected($template->category == '')
                    >
                        {{ __('Default') }}
                    </option>
                    @foreach ($categoryList as $category)
                        <option
                            value="{{ $category->name }}"
                            @selected($template->category == $category->name)
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach
                @else
                    <option
                        value=""
                        selected
                    >
                        {{ __('Default') }}
                    </option>
                    @foreach ($categoryList as $category)
                        <option value="{{ $category->name }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                @endif
            </x-forms.input>

            <x-forms.input
                id="short_name"
                name="short_name"
                label="{{ __('Template Short Name') }}"
                size="lg"
                placeholder="{{ __('Template Short Name') }}"
                value="{{ $template != null ? $template->short_name : null }}"
                tooltip="{{ __('Shortened name of the template or human name. Maximum 3 letters is suggested.') }}"
            />

            <x-forms.input
                id="description"
                name="description"
                label="{{ __('Description') }}"
                size="lg"
                placeholder="{{ __('Description') }}"
                tooltip="{{ __('A short description of what this chat template can help with.') }}"
                type="textarea"
                rows="3"
            >{{ $template != null ? $template->description : null }}</x-forms.input>

            <x-forms.input
                    id="first_message"
                    name="first_message"
                    value="{{ old('first_message', $template?->first_message) }}"
                    label="{{ __('First Message') }}"
                    size="lg"
            />

            <x-forms.input
                    id="instructions"
                    type="textarea"
                    name="instructions"
                    label="{{ __('Instructions') }}"
                    rows="5"
                    tooltip="{{ __('You can provide instructions to your GPT-3 model to ensure it aligns with your brand and tone.') }}"
            >{{ $template?->instructions }}</x-forms.input>


            <x-forms.input
                id="avatar"
                name="avatar"
                label="{{ __('Avatar') }}"
                size="lg"
                type="file"
                tooltip="{{ __('Avatar will shown in chat page.') }}"
                value="{{ $template != null ? $template->short_name : null }}"
            />

            <x-forms.input
                id="color"
                name="color"
                label="{{ __('Template Color') }}"
                size="lg"
                type="color"
                value="{{ $template != null ? $template->color : '#8fd2d0' }}"
                tooltip="{{ __('Pick a color for for the icon container shape. Color is in HEX format.') }}"
            />
        </div>

        <div class="flex flex-col gap-5">
            <x-form-step
                step="2"
                label="{{ __('Personality') }}"
            />

            <x-forms.input
                id="human_name"
                name="human_name"
                label="{{ __('Human Name') }}"
                size="lg"
                placeholder="{{ __('Allison Burgers') }}"
                value="{{ $template != null ? $template->human_name : null }}"
                tooltip="{{ __('Define a human name for the chatbot to give it more personality.') }}"
            />

            <x-forms.input
                id="role"
                name="role"
                label="{{ __('Template Role') }}"
                size="lg"
                placeholder="{{ __('Finance Expert') }}"
                value="{{ $template != null ? $template->role : null }}"
                tooltip="{{ __('A role for the chatbot that can define what it can help with. For example Finance Expert.') }}"
            />

            <x-forms.input
                id="helps_with"
                name="helps_with"
                label="{{ __('Helps With') }}"
                size="lg"
                placeholder="{{ __('I can help you with managing your finance') }}"
                tooltip="{{ __('Describe what this chatbot can help with. It shows when starting a conversation and the chatbot introducing itself.') }}"
                type="textarea"
                rows="3"
            >{{ $template != null ? $template->helps_with : null }}</x-forms.input>

            <div class="mb-[20px]">
                <label class="form-label" for="chatbot_id">
                    {{__('Chatbot Training')}}
                    <x-info-tooltip text="{{__('Choose any trained chatbot. If you need to train a new chatbot, visit the Chatbot Training')}}" />
                </label>
                <select name="chatbot_id" id="chatbot_id" class="form-control">
                    <option value="0">Select Chatbot</option>
                    @foreach($chatbots as $chatbot)
                        <option {{ $template?->chatbot_id == $chatbot->id ? 'selected': '' }} value="{{ $chatbot->id }}" > {{ $chatbot->title }} </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-[20px]">
                <label class="form-label" for="assistant">
                    {{__('AI Assistants')}}
                    <x-info-tooltip text="{{__('Choose your Openai Assistant. If you need to train a new chatbot, visit the Chatbot Training')}}" />
                </label>
                <select name="assistant" id="assistant" class="form-control">
                    <option value="">Select Assistant</option>
                    @foreach($assistants as $assistant)
                        <option {{ $template?->assistant == $assistant["id"] ? 'selected': '' }} value="{{ $assistant["id"] }}" > {{ $assistant["name"] }} </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-3">
                <div class="flex flex-wrap items-center gap-3">
                    <label>
                        {{ __('Chatbot Training') }}
                        <x-info-tooltip
                            text="{{ __('Chat models take a list of messages as input and return a model-generated message as output. Although the chat format is designed to make multi-turn conversations easy, it’s just as useful for single-turn tasks without any conversation. Add your custom JSON data.') }}"
                        />
                    </label>
                    <x-button
                        class="chat-completions-fill-btn"
                        type="button"
                        size="sm"
                    >
                        {{ __('Create example input') }}
                    </x-button>
                </div>

                <x-forms.input
                    id="chat_completions"
                    size="lg"
                    type="textarea"
                    rows="3"
                >{{ $template != null ? $template->chat_completions : null }}</x-forms.input>
                <textarea
                        id="hidden_chat_completions"
                        name="chat_completions"
                        hidden=""
                ></textarea>
                <x-button
                    class="justify-start"
                    variant="link"
                    href="https://platform.openai.com/docs/guides/gpt/chat-completions-api"
                    target="_blank"
                >
                    {{ __('More Info') }}
                    <x-tabler-arrow-up-right class="size-4" />
                </x-button>
            </div>
        </div>

        <x-button
            id="custom_template_button"
            size="lg"
            type="submit"
        >
            {{ __('Save') }}
        </x-button>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/openai.js') }}"></script>
    <script
        src="{{ custom_theme_url('/assets/libs/ace/src-min-noconflict/ace.js') }}"
        type="text/javascript"
        charset="utf-8"
    ></script>
    <style
        type="text/css"
        media="screen"
    >
        #chat_completions {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        .ace_editor {
            min-height: 200px;
        }
    </style>
    <script>
		const editor_chat_completions = ace.edit('chat_completions');
		editor_chat_completions.session.setMode("ace/mode/json");

		$('#custom_template_form').submit(function(event) {
			$('#hidden_chat_completions').val(editor_chat_completions.getValue());
		});
    </script>
@endpush
