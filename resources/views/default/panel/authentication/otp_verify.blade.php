@extends('panel.authentication.layout.app')
@section('title', __('OTP Verify'))

@section('form')
    <div class="relative font-inter antialiased">
        <main class="relative flex flex-col justify-center overflow-hidden">
            <div class="w-full max-w-6xl mx-auto px-4 md:px-6">
                <div class="flex justify-center">
                    <div class="max-w-md mx-auto text-center bg-white px-4 sm:px-8 py-10 rounded-xl shadow">
                        <header class="mb-8">
                            <h1 class="text-2xl font-bold mb-3">{{ __("Email Verification") }}</h1>
                            <p class="text-[15px] text-gray-700">{{ __("Enter the 4-digit verification code that was sent to your email.") }}</p>
                        </header>
                        <div id="error-container" class="mb-4 text-red-500"></div>
                        <form id="otp-form" method="POST" action="{{ route('verify-otp') }}">
                            @csrf
                            <div class="flex items-center justify-center gap-3">
                                <input type="text" name="otp[]"
                                       class="w-14 h-14 text-center text-2xl font-extrabold text-gray-900 bg-gray-200 border border-transparent hover:border-gray-400 appearance-none rounded p-4 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                       pattern="\d*" maxlength="1"/>
                                <input type="text" name="otp[]"
                                       class="w-14 h-14 text-center text-2xl font-extrabold text-gray-900 bg-gray-200 border border-transparent hover:border-gray-400 appearance-none rounded p-4 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                       maxlength="1"/>
                                <input type="text" name="otp[]"
                                       class="w-14 h-14 text-center text-2xl font-extrabold text-gray-900 bg-gray-200 border border-transparent hover:border-gray-400 appearance-none rounded p-4 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                       maxlength="1"/>
                                <input type="text" name="otp[]"
                                       class="w-14 h-14 text-center text-2xl font-extrabold text-gray-900 bg-gray-200 border border-transparent hover:border-gray-400 appearance-none rounded p-4 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                       maxlength="1"/>
                            </div>
                            <div class="max-w-[260px] mx-auto mt-4">
                                <button type="submit" id="otpSubmitButton"
                                        class="w-full inline-flex justify-center whitespace-nowrap rounded-lg bg-blue-500 px-3.5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-300 focus-visible:outline-none focus-visible:ring focus-visible:ring-blue-300 transition-colors duration-150">
                                    {{ __("Verify Account") }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('otp-form');
        const inputs = [...form.querySelectorAll('input[type=text]')];
        const submitButton = form.querySelector('button[type=submit]');
        const errorContainer = document.getElementById('error-container');

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(form);

            submitButton.disabled = true;
            submitButton.innerHTML = 'Verifying...';

            $.ajax({
                type: 'POST',
                url: form.action,
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function (data) {
                    toastr.success('{{ __("Verification successful, redirecting...") }}');
                    window.location.href = data.link;
                },
                error: function (data) {
                    if (data.responseJSON.errors) {
                        var err = data.responseJSON.errors;
                        $.each(err, function (index, value) {
                            toastr.error(value);
                        });
                    } else if (data.responseJSON.message) {
                        toastr.error(data.responseJSON.message);
                    }
                    submitButton.disabled = false;
                    submitButton.innerHTML = '{{ __("Verify Account") }}';
                }
            });
        });

        const handleKeyDown = (e) => {
            if (!/^[0-9]{1}$/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab' && !e.metaKey) {
                e.preventDefault();
            }

            if (e.key === 'Delete' || e.key === 'Backspace') {
                const index = inputs.indexOf(e.target);
                if (index > 0) {
                    inputs[index - 1].value = '';
                    inputs[index - 1].focus();
                }
            }
        };

        const handleInput = (e) => {
            const {target} = e;
            const index = inputs.indexOf(target);
            if (target.value) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                } else {
                    submitButton.focus();
                }
            }
        };

        const handleFocus = (e) => {
            e.target.select();
        };

        const handlePaste = (e) => {
            e.preventDefault();
            const text = e.clipboardData.getData('text');
            if (!new RegExp(`^[0-9]{${inputs.length}}$`).test(text)) {
                return;
            }
            const digits = text.split('');
            inputs.forEach((input, index) => input.value = digits[index]);
            submitButton.focus();
        };

        inputs.forEach((input) => {
            input.addEventListener('input', handleInput);
            input.addEventListener('keydown', handleKeyDown);
            input.addEventListener('focus', handleFocus);
            input.addEventListener('paste', handlePaste);
        });
    });
</script>
