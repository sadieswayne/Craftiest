@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', $item != null ? __('Edit Tool') : __('Add New Tool'))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-5"
        id="item_form"
        onsubmit="return toolsCreateOrUpdate({{ $item != null ? $item->id : null }});"
        enctype="multipart/form-data"
    >
        <x-forms.input
            id="image"
            type="file"
            name="image"
            accept="image/*"
            label="{{ __('Image') }}"
            size="lg"
            required
        />

        <x-forms.input
            id="title"
            label="{{ __('Title') }}"
            name="title"
            size="lg"
            required
            value="{{ $item != null ? $item->title : null }}"
        />

        <x-forms.input
            id="description"
            name="description"
            type="textarea"
            label="{{ __('Description') }}"
            rows="10"
            required
        >{{ $item != null ? $item->description : null }}</x-forms.input>

        <x-forms.input
            id="buy_link"
            label="{{ __('Buy Link Text') }}"
            name="buy_link"
            size="lg"
            value="{{ $item != null ? $item->buy_link : null }}"
        />

        <x-forms.input
            id="buy_link_url"
            label="{{ __('Buy Link URL') }}"
            name="buy_link_url"
            size="lg"
            value="{{ $item != null ? $item->buy_link_url : null }}"
        />

        <x-forms.input
            id="learn_more_link"
            label="{{ __('Learn More Link Text') }}"
            name="learn_more_link"
            size="lg"
            value="{{ $item != null ? $item->learn_more_link : null }}"
        />

        <x-forms.input
            id="learn_more_link_url"
            label="{{ __('Learn More Link URL') }}"
            name="learn_more_link_url"
            size="lg"
            value="{{ $item != null ? $item->learn_more_link_url : null }}"
        />

        <x-button
            id="item_button"
            size="lg"
            type="submit"
        >
            {{ __('Save') }}
        </x-button>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
@endpush
