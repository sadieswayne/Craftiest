@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', __('Integration Edit'))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-5"
        enctype="multipart/form-data"
        method="post"
        action="{{ route('dashboard.user.integration.update', $item->id) }}"
    >
        @csrf
        @method('put')

        @foreach ($credentials as $field)
            <x-forms.input
                id="{{ $field['name'] }}"
                type="{{ $field['type'] }}"
                name="{{ $field['name'] }}"
                value="{{ $field['value'] ?? '' }}"
                label="{{ $field['label'] }}"
                size="lg"
            />
        @endforeach

        <x-button
            class="w-full"
            size="lg"
            type="submit"
        >
            {{ __('Save') }}
        </x-button>
    </form>
@endsection
