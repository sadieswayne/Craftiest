@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', __($title))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-wrap justify-between gap-y-5"
        action="{{ $action }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method($method)

        <x-alert>
            <p>
                @lang('The cloned voice will appear in the list of voices available on AI Voiceover')
            </p>
        </x-alert>

        <x-forms.input
            class:container="w-full md:w-[48%]"
            id="name"
            name="name"
            size="lg"
            label="{{ __('Name') }}"
            value="{{ old('name', $item?->name) }}"
            required
        >
            @error('name')
                <p class="mt-3 text-red-500">
                    {{ $message }}
                </p>
            @enderror
        </x-forms.input>

        <x-forms.input
            class:container="w-full md:w-[48%]"
            id="status"
            label="{{ __('Status') }}"
            name="status"
            size="lg"
            type="select"
        >
            <option
                @selected($item?->status == '1')
                value="1"
            >
                {{ __('Active') }}
            </option>
            <option
                @selected($item?->status == '0')
                value="0"
            >
                {{ __('Passive') }}
            </option>
        </x-forms.input>

        @if (!$item?->id)
            <x-forms.input
                class:container="w-full"
                id="file"
                label="{{ __('File') }}"
                type="file"
                name="file"
                size="lg"
            >
                @error('file')
                    <p class="mt-3 text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </x-forms.input>
        @endif

        <x-button
            class="w-full"
            size="lg"
            type="{{ $app_is_demo ? 'button' : 'submit' }}"
            onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
        >
            {{ __('Add voice') }}
        </x-button>
    </form>
@endsection
