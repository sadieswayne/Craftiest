@php
    $categories = ['General Inquiry', 'Technical Issue', 'Improvement Idea', 'Feedback', 'Other'];
    $priorities = ['Low', 'Normal', 'High', 'Critical'];
@endphp

@extends('panel.layout.app')
@section('title', __('New Support Request'))
@section('titlebar_actions', '')
@section('titlebar_title', __('Create New Support Request'))
@section('titlebar_subtitle', __('Create new support request. We will answer as soon as possible.'))

@section('content')
    <div class="py-10">
        <form
            class="mx-auto flex w-full flex-wrap justify-between gap-y-5 lg:w-5/12"
            id="support_form"
            onsubmit="return sendSupportForm();"
        >
            <x-forms.input
                class:container="w-full md:w-[48%]"
                id="category"
                type="select"
                name="category"
                required
                label="{{ __('Support Category') }}"
                size="lg"
            >
                @foreach ($categories as $category)
                    <option
                        value="{{ $category }}"
                        @selected($loop->first)
                    >
                        {{ __($category) }}
                    </option>
                @endforeach
            </x-forms.input>
            <x-forms.input
                class:container="w-full md:w-[48%]"
                id="priority"
                name="priority"
                type="select"
                required
                label="{{ __('Support Priority') }}"
                size="lg"
            >
                @foreach ($priorities as $priority)
                    <option
                        value="{{ $priority }}"
                        @selected($loop->first)
                    >
                        {{ __($priority) }}
                    </option>
                @endforeach
            </x-forms.input>

            <x-forms.input
                class:container="w-full"
                id="subject"
                name="subject"
                placeholder="{{ __('Please enter subject of the support request') }}"
                required
                size="lg"
                label="{{ __('Subject') }}"
            />

            <x-forms.input
                class:container="w-full"
                id="message"
                name="message"
                rows="5"
                type="textarea"
                placeholder="{{ __('Please enter your message') }}"
                required
                size="lg"
                label="{{ __('Message') }}"
            />

            <x-button
                class="w-full"
                id="support_button"
                size="lg"
                type="submit"
            >
                {{ __('Send') }}
            </x-button>
        </form>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/support.js') }}"></script>
@endpush
