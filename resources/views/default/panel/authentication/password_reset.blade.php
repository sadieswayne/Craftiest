@extends('panel.authentication.layout.app')
@section('title', __('Reset Password'))

@section('form')
    <h1 class="mb-[25px]">{{ __('Forgot Password') }}</h1>
    <form
        class="flex flex-col gap-6"
        id="password_reset_form"
        novalidate="novalidate"
        onsubmit="return PasswordResetMailForm();"
    >
        <x-forms.input
            id="password_reset_email"
            type="email"
            placeholder="{{ __('your@email.com') }}"
            label="{{ __('Email Address') }}"
            size="lg"
        />
        <x-button
            class="text-sm"
            id="PasswordResetFormButton"
            type="submit"
            tag="button"
            form="password_reset_form"
        >
            {{ __('Send Instructions') }}
        </x-button>
        <!-- TODO Openai Demo -->
    </form>
@endsection
