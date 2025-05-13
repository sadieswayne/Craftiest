@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', $item != null ? __('Edit Generator') : __('Add Generator'))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-5"
        id="item_form"
        onsubmit="return generatorlistCreateOrUpdate({{ $item != null ? $item->id : null }});"
    >
        <x-forms.input
            id="menu_title"
            label="{{ __('Menu Title') }}"
            name="menu_title"
            size="lg"
            required
            value="{{ $item != null ? $item->menu_title : '' }}"
        />

        <x-forms.input
            id="subtitle_one"
            label="{{ __('Subtitle One') }}"
            name="subtitle_one"
            size="lg"
            required
            value="{{ $item != null ? $item->subtitle_one : '' }}"
        />

        <x-forms.input
            id="subtitle_two"
            name="subtitle_two"
            value="{{ $item != null ? $item->subtitle_two : '' }}"
            required
            size="lg"
            label="{{ __('Subtitle Two') }}"
        />

        <x-forms.input
            id="icon"
            label="{{ __('Icon') }}"
            name="icon"
            size="lg"
            value="{!! $item != null ? ($item->icon  ? : '') : '' !!}"
        />

        <x-forms.input
            id="title"
            label="{{ __('Title') }}"
            name="title"
            size="lg"
            required
            value="{{ $item != null ? $item->title : '' }}"
        />

        <x-forms.input
            id="text"
            name="text"
            type="textarea"
            label="{{ __('Text') }}"
            rows="10"
            required
        >{{ $item != null ? $item->text : '' }}</x-forms.input>

        <x-forms.input
            id="image"
            type="file"
            name="image"
            accept="image/*"
            size="lg"
            label="{{ __('Image') }}"
        />

        <x-forms.input
            id="image_title"
            name="image_title"
            value="{{ $item != null ? $item->image_title : '' }}"
            required
            size="lg"
            label="{{ __('Image Title') }}"
        />

        <x-forms.input
            id="image_subtitle"
            name="image_subtitle"
            value="{{ $item != null ? $item->image_subtitle : 'null' }}"
            required
            size="lg"
            label="{{ __('Image Subtitle') }}"
        />

        <x-forms.input
            id="color"
            name="color"
            size="lg"
            type="color"
            value="{{ $item != null ? $item->color : '#8fd2d0' }}"
            required
            label="{{ __('Color Code (Please enter like code. For example: #FFFFFF)') }}"
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
