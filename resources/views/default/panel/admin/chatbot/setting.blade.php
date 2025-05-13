@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', $title)
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-5"
        id="user_edit_form"
        method="post"
        enctype="multipart/form-data"
        action="{{ $action }}"
    >
        @csrf
        @method($method)

        <div class="space-y-3">
            <x-forms.input
                id="chatbot_status"
                label="{{ __('Show ChatBot Ballon on') }}"
                type="select"
                size="lg"
                name="chatbot_status"
            >
                <option
                    value="disabled"
                    @selected($settings_two->chatbot_status == 'disabled')
                >
                    {{ __('Disabled') }}
                </option>
                <option
                    value="frontend"
                    @selected($settings_two->chatbot_status == 'frontend')
                >
                    {{ __('Frontend') }}
                </option>
                <option
                    value="dashboard"
                    @selected($settings_two->chatbot_status == 'dashboard')
                >
                    {{ __('Dashboard') }}
                </option>
                <option
                    value="both"
                    @selected($settings_two->chatbot_status == 'both')
                >
                    {{ __('Both') }}
                </option>
            </x-forms.input>
            @error('chatbot_status')
                <p class="text-red-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="space-y-3">
            <x-forms.input
                id="chatbot_template"
                name="chatbot_template"
                label="{{ __('Template') }}"
                type="select"
                size="lg"
            >
                @foreach ($chatbotData as $chatbot)
                    <option
                        value="{{ $chatbot->id }}"
                        @selected($settings_two->chatbot_template == $chatbot->id)
                    >
                        {{ __($chatbot->title) }}
                    </option>
                @endforeach
            </x-forms.input>
            @error('chatbot_template')
                <p class="text-red-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="space-y-3">
            <x-forms.input
                type="select"
                size="lg"
                name="position"
                label="{{ __('Position') }}"
            >
                <option
                    value="top-left"
                    @selected($settings_two->chatbot_position == 'top-left')
                >
                    {{ __('Top Left') }}
                </option>
                <option
                    value="top-right"
                    @selected($settings_two->chatbot_position == 'top-right')
                >
                    {{ __('Top Right') }}
                </option>
                <option
                    value="bottom-right"
                    @selected($settings_two->chatbot_position == 'bottom-right')
                >
                    {{ __('Bottom Right') }}
                </option>
                <option
                    value="bottom-left"
                    @selected($settings_two->chatbot_position == 'bottom-left')
                >
                    {{ __('Bottom Left') }}
                </option>
            </x-forms.input>
        </div>

        <div class="space-y-3">
            <x-forms.input
                id="chatbot_rate_limit"
                type="number"
                name="chatbot_rate_limit"
                min="2"
                max="50"
                default="2"
                size="lg"
                label="{{ __('Message Limit per day') }}"
                value="{{ $settings_two->chatbot_rate_limit }}"
            />
            @error('chatbot_rate_limit')
                <p class="text-red-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="space-y-2">
            <x-forms.input
                id="first_message"
                name="first_message"
                value="{{ old('first_message', $chatbot?->first_message) }}"
                label="{{ __('First Message') }}"
                size="lg"
            />
            @error('first_message')
                <p class="text-red-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="space-y-2">
            <x-forms.input
                id="instructions"
                type="textarea"
                name="instructions"
                label="{{ __('Instructions') }}"
                rows="5"
                tooltip="{{ __('You can provide instructions to your GPT-3 model to ensure it aligns with your brand and tone.') }}"
            >{{ old('instructions', $chatbot?->instructions) }}</x-forms.input>
            @error('instructions')
                <p class="text-red-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <x-forms.input
            id="chatbot_login_require"
            size="lg"
            name="chatbot_login_require"
            type="checkbox"
            label="{{ __('Disable if user not logged in?') }}"
            :checked="$settings_two->chatbot_login_require == true"
            switcher
        />

        <x-forms.input
            id="chatbot_show_timestamp"
            name="chatbot_show_timestamp"
            size="lg"
            type="checkbox"
            label="{{ __('Show timestamp?') }}"
            :checked="$settings_two->chatbot_show_timestamp == true"
            switcher
        />

        <x-button
            size="lg"
            type="submit"
        >
            {{ __('Save') }}
        </x-button>
    </form>
@endsection
