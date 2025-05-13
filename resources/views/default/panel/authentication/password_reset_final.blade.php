@extends('panel.authentication.layout.app')
@section('title', __('Reset Password'))

@section('form')
    <h1 class="mb-[25px]">{{ __('Change Password') }}</h1>
    <form
        class="flex flex-col gap-6"
        id="password_reset_form"
        novalidate="novalidate"
        onsubmit="return PasswordReset('{{ $user->password_reset_code }}');"
    >
        <x-forms.input
            id="password_register"
            type="password"
            placeholder="{{ __('Your password') }}"
            label="{{ __('Password') }}"
            size="lg"
            required
        />
        <x-forms.input
            id="password_confirmation_register"
            type="password"
            placeholder="{{ __('Password confirmation') }}"
            label="{{ __('Confirm Your Password') }}"
            size="lg"
            required
        />
        <x-button
            class="text-sm"
            id="PasswordResetFormButton"
            type="submit"
            tag="button"
            form="password_reset_form"
        >
            {{ __('Reset Password') }}
        </x-button>
        <!-- TODO Openai Demo -->
    </form>
@endsection
