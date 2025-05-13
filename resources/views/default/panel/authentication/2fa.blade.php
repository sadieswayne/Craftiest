@extends('panel.authentication.layout.app')
@section('title', __('Sign in'))

@section('form')
    <h1 class="mb-11">{{ __('2 Fa Login') }}</h1>
    <form
        class="flex flex-col gap-6"
        action="{{ route('2fa.login') }}"
        novalidate="novalidate"
        method="post"
    >
        @csrf
        <input
            type="hidden"
            name="user_id"
            value="{{ session('user_id') }}"
        >
        <x-forms.input
            id="one_time_password"
            name="one_time_password"
            type="text"
            placeholder="{{ __('000 000') }}"
            value=""
            label="{{ __('One time password') }}"
            size="lg"
        />

        <x-button
            class="text-sm"
            id="LoginFormButton"
            size="lg"
            type="submit"
            tag="button"
        >
            {{ __('Verify') }}
        </x-button>
    </form>
@endsection
