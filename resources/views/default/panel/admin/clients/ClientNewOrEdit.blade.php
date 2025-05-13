@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', isset($client) ? __('Edit Client') : __('Create New Client'))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-5"
        id="item_edit_form"
        onsubmit="return clientSave({{ $client->id ?? null }});"
        enctype="multipart/form-data"
    >
        @if (isset($client))
            <img
                class="size-12 rounded-full object-cover object-center"
                src="{{ url('') . isset($client) ? (str_starts_with($client->avatar, 'asset') ? custom_theme_url($client->avatar) : '/clientAvatar/' . $client->avatar) : custom_theme_url('assets/img/auth/default-avatar.png') }}"
                alt="Avatar"
            />
        @endif

        <x-forms.input
            id="avatar"
            type="file"
            name="avatar"
            size="lg"
            label="{{ __('Avatar') }}"
            value="{{ isset($client) ? $client->avatar : null }}"
            accept="image/png, image/jpeg, image/svg"
        />

        <x-forms.input
            id="client_alt"
            name="client_alt"
            size="lg"
            label="{{ __('Alt') }}"
            value="{{ isset($client) ? $client->alt : null }}"
            required
        />

        <x-forms.input
            id="client_title"
            name="client_title"
            size="lg"
            label="{{ __('Title') }}"
            value="{{ isset($client) ? $client->title : null }}"
            required
        />

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
    <script src="{{ custom_theme_url('/assets/js/panel/client.js') }}"></script>
@endpush
