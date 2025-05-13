@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', __('Two Factor Authentication'))
@section('titlebar_actions', '')

@section('settings')
    @if (Google2FA::isActivated())
        <div class="text-center">
            <h2 class="mb-12">
                @lang('2FA is activated!')
            </h2>
            <x-button
                class="w-full"
                variant="danger"
                size="lg"
                href="{{ route('dashboard.user.2fa.deactivate') }}"
            >
                {{ __('Deactivate 2FA') }}
            </x-button>
        </div>
    @else
        <form
            action="{{ route('dashboard.user.2fa.activate') }}"
            method="post"
        >
            @csrf
            <input
                type="hidden"
                name="google2fa_secret"
                value="{{ $secret }}"
            >

            <h2 class="mb-12">
                @lang('Enable 2FA')
            </h2>

            <div class="mb-8">
                <x-form-step
                    class="mb-2.5 bg-transparent p-0"
                    step="1"
                    label="{{ __('Install Google Authenticator App') }}"
                />
                <p class="ps-8 text-sm font-medium lg:w-9/12">
                    @lang("To enable 2FA, you'll need the Google Authenticator App. You can download it from the App Store or Google PlayStore.")
                </p>
            </div>

            <div class="mb-8">
                <x-form-step
                    class="mb-2.5 bg-transparent p-0"
                    step="2"
                    label="{{ __('Scan the QR Code.') }}"
                />
                <p class="ps-8 text-sm font-medium lg:w-9/12">
                    @lang("Use your phone's camera to scan the QR Code below. If scanning isn't an option, you can manually input the key into your app.")

                    <span class="mt-5 block opacity-50">
                        @lang('Manual Key:')
                    </span>
                    <span class="twofa-secret-key-wrap">
                        <span class="twofa-secret-key">
                            {!! $secret !!}
                        </span>
                        <button
                            class="lqd-clipboard-copy size-5 pointer-events-auto ms-2 inline-flex items-center justify-center transition-all hover:-translate-y-[2px] hover:scale-110"
                            data-copy-options='{ "content": ".twofa-secret-key", "contentIn": "<.twofa-secret-key-wrap" }'
                            type="button"
                            title="{{ __('Copy to clipboard') }}"
                        >
                            <span class="sr-only">{{ __('Copy to clipboard') }}</span>
                            <x-tabler-copy class="size-4" />
                        </button>
                    </span>
                </p>
            </div>

            <div class="mb-8 flex items-center justify-center rounded-xl border p-4 md:p-14 lg:w-9/12">
                {!! $qrCode !!}
            </div>

            <div class="mb-20">
                <x-form-step
                    class="mb-2.5 bg-transparent p-0"
                    step="2"
                    label="{{ __('Scan the QR Code.') }}"
                />
                <p class="mb-10 ps-8 text-sm font-medium lg:w-9/12">
                    @lang('Please input the 6-digit verification code displayed on your Google Authenticator app to finalize the process and activate 2FA.')
                </p>

                <x-forms.input
                    id="otp"
                    size="xl"
                    name="one_time_password"
                />
            </div>

            <x-button
                class="w-full"
                size="lg"
                type="submit"
            >
                {{ __('Enable 2FA') }}
            </x-button>
        </form>
    @endif

@endsection

@push('script')
@endpush
