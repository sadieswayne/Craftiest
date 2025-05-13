@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', $title)
@section('titlebar_actions')
@endsection

@section('content')
    <div class="py-10">
        <form
            class="mx-auto flex w-full flex-col gap-5 lg:w-5/12"
            id="user_edit_form"
            method="post"
            enctype="multipart/form-data"
            action="{{ $action }}"
        >
            @csrf
            @method($method)
            <input
                type="hidden"
                name="model"
                value="{{ \App\Helpers\Classes\Helper::setting('openai_default_model') }}"
            >
            <p>
                {{ __('You have the ability to provide directives to your personalized GPT and tailor it according to your preferences, ensuring it aligns seamlessly with your brand and tone.') }}
            </p>

            <x-form-step
                step="1"
                label="{{ __('General') }}"
            />

            <div class="space-y-2">
                <x-forms.input
                    id="title"
                    name="title"
                    value="{{ old('title', $item->title) }}"
                    label="{{ __('Title') }}"
                    size="lg"
                />
                @error('title')
                    <p class="text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="space-y-2">
                <x-forms.input
                    id="role"
                    name="role"
                    value="{{ old('role', $item->role) }}"
                    label="{{ __('Role') }}"
                    size="lg"
                />
                @error('role')
                    <p class="text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <x-button
                id="user_edit_button"
                type="submit"
                size="lg"
            >
                {{ __('Save') }}
            </x-button>
        </form>
    </div>
@endsection
@push('script')
@endpush
