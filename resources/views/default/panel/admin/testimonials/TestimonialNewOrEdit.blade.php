@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', isset($testimonial) ? __('Edit Testimonial') : __('Create New Testimonial'))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-5"
        id="item_edit_form"
        onsubmit="return testimonialSave({{ $testimonial->id ?? null }});"
        enctype="multipart/form-data"
    >
        @if (isset($testimonial))
            <img
                class="size-20 mx-auto rounded-full object-cover object-center"
                src="{{ url('') . isset($testimonial) ? (str_starts_with($testimonial->avatar, 'asset') ? custom_theme_url($testimonial->avatar) : '/testimonialAvatar/' . $testimonial->avatar) : custom_theme_url('/assets/img/auth/default-avatar.png') }}"
                alt="Avatar"
            />
        @endif

        <x-forms.input
            id="avatar"
            type="file"
            name="avatar"
            size="lg"
            label="{{ __('Avatar') }}"
            value="{{ isset($testimonial) ? $testimonial->avatar : null }}"
            accept="image/png, image/jpeg"
        />

        <x-forms.input
            id="full_name"
            type="text"
            name="full_name"
            label="{{ __('Full Name') }}"
            value="{{ isset($testimonial) ? $testimonial->full_name : null }}"
            required
        />

        <x-forms.input
            id="job_title"
            name="job_title"
            label="{{ __('Job Title') }}"
            value="{{ isset($testimonial) ? $testimonial->job_title : null }}"
            required
        />

        <x-forms.input
            id="words"
            name="words"
            label="{{ __('Testimonial Text') }}"
            value="{{ isset($testimonial) ? $testimonial->words : null }}"
            type="textarea"
            rows="10"
            required
        >{{ isset($testimonial) ? $testimonial->words : null }}</x-forms.input>

        <x-button
            id="item_edit_button"
            size="lg"
            type="submit"
        >
            {{ __('Save') }}
        </x-button>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/testimonial.js') }}"></script>
@endpush
