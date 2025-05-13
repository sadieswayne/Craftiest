@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', __('Google Adsense Edit'))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex w-full flex-col gap-5"
        @if ($app_is_not_demo) action="{{ $app_is_demo ? '#' : route('dashboard.admin.ads.update', [$id->id]) }}"
			method="POST"
			enctype="multipart/form-data" @endif
        @if ($app_is_demo) onsubmit="return toastr.info('This feature is disabled in Demo version.');" @endif
    >
        @method('PUT')
        @csrf

        <div>
            <h3 class="mb-4">
                {{ __('Edit Google Adsense Code') }}:
                <span class="font-bold text-primary">
                    {{ $id->type }}
                </span>
            </h3>
            <hr>
        </div>

        <x-forms.input
            id="asense_status"
            name="status"
            type="checkbox"
            :checked="$id->status == true"
            label="{{ __('Adsense Status') }}"
            switcher
        />

        <div class="flex flex-col gap-3">
            <x-forms.input
                id="richtext"
                name="code"
                rows="12"
                required
                type="textarea"
                label="{{ __('Adsense Code') }}"
            >{{ $id->code }}</x-forms.input>
            @error('code')
                <p class="text-red-500">
                    {{ $errors->first('code') }}
                </p>
            @enderror
        </div>

        <x-button
            type="submit"
            size="lg"
        >
            {{ __('Save') }}
        </x-button>
    </form>
@endsection
