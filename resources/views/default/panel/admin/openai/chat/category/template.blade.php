@extends('panel.layout.settings')
@section('title', __('Add or Edit Category'))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-5"
        id="custom_template_form"
        onsubmit="return categorySave({{ $item != null ? $item->id : null }});"
    >
        <x-form-step
            step="1"
            label="{{ __('Template') }}"
        />

        <x-forms.input
            id="category_name"
            name="category_name"
            label="{{ __('Category Name') }}"
            size="lg"
            placeholder="{{ __('Category Name') }}"
            value="{{ $item != null ? $item->name : null }}"
            tooltip="{{ __('Category name for Custom AI Writers') }}"
        />

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
    <script src="{{ custom_theme_url('/assets/js/panel/chat_categories.js') }}"></script>
@endpush
