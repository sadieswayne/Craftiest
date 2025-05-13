@extends('panel.authentication.layout.app')
@section('title', __('Sign in'))

@section('form')
    <h1 class="mb-11">{{ __('Sign in') }}</h1>
    @if ($setting->github_active || $setting->twitter_active || $setting->google_active || $setting->facebook_active)
        <div class="flex flex-wrap justify-between gap-4">
            @if ($setting->google_active)
                <x-button class="grow basis-full justify-start rounded-xl sm:basis-[45%]" size="lg" variant="outline"
                    href="{{ route('login.google') }}">
                    <svg class="shrink-0" width="22" height="22" viewBox="0 0 25 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M23.001 12.2332C23.001 11.3699 22.9296 10.7399 22.7748 10.0865H12.7153V13.9832H18.62C18.501 14.9515 17.8582 16.4099 16.4296 17.3898L16.4096 17.5203L19.5902 19.935L19.8106 19.9565C21.8343 18.1249 23.001 15.4298 23.001 12.2332Z"
                            fill="#4285F4" />
                        <path
                            d="M12.714 22.5C15.6068 22.5 18.0353 21.5666 19.8092 19.9567L16.4282 17.3899C15.5235 18.0083 14.3092 18.4399 12.714 18.4399C9.88069 18.4399 7.47596 16.6083 6.61874 14.0766L6.49309 14.0871L3.18583 16.5954L3.14258 16.7132C4.90446 20.1433 8.5235 22.5 12.714 22.5Z"
                            fill="#34A853" />
                        <path
                            d="M6.62046 14.0767C6.39428 13.4234 6.26337 12.7233 6.26337 12C6.26337 11.2767 6.39428 10.5767 6.60856 9.92337L6.60257 9.78423L3.25386 7.2356L3.14429 7.28667C2.41814 8.71002 2.00146 10.3084 2.00146 12C2.00146 13.6917 2.41814 15.29 3.14429 16.7133L6.62046 14.0767Z"
                            fill="#FBBC05" />
                        <path
                            d="M12.7141 5.55997C14.7259 5.55997 16.083 6.41163 16.8569 7.12335L19.8807 4.23C18.0236 2.53834 15.6069 1.5 12.7141 1.5C8.52353 1.5 4.90447 3.85665 3.14258 7.28662L6.60686 9.92332C7.47598 7.39166 9.88073 5.55997 12.7141 5.55997Z"
                            fill="#EB4335" />
                    </svg>
                    {{ __('Login with Google') }}
                </x-button>
            @endif
            @if ($setting->github_active)
                <x-button class="grow basis-full justify-start rounded-xl sm:basis-[45%]" size="lg" variant="outline"
                    href="{{ route('login.github') }}">
                    <svg class="shrink-0" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M12.0099 0C5.36875 0 0 5.40833 0 12.0992C0 17.4475 3.43994 21.9748 8.21205 23.5771C8.80869 23.6976 9.02724 23.3168 9.02724 22.9965C9.02724 22.716 9.00757 21.7545 9.00757 20.7527C5.6667 21.474 4.97099 19.3104 4.97099 19.3104C4.43409 17.9082 3.63858 17.5478 3.63858 17.5478C2.54511 16.8066 3.71823 16.8066 3.71823 16.8066C4.93117 16.8868 5.56763 18.0486 5.56763 18.0486C6.64118 19.8913 8.37111 19.3707 9.06706 19.0501C9.16638 18.2688 9.48473 17.728 9.82275 17.4276C7.15817 17.1471 4.35469 16.1055 4.35469 11.458C4.35469 10.1359 4.8316 9.05428 5.58729 8.21304C5.46807 7.91263 5.0504 6.67043 5.70677 5.00787C5.70677 5.00787 6.72083 4.6873 9.00732 6.24981C9.98625 5.98497 10.9958 5.85024 12.0099 5.84911C13.024 5.84911 14.0577 5.98948 15.0123 6.24981C17.299 4.6873 18.3131 5.00787 18.3131 5.00787C18.9695 6.67043 18.5515 7.91263 18.4323 8.21304C19.2079 9.05428 19.6652 10.1359 19.6652 11.458C19.6652 16.1055 16.8617 17.1269 14.1772 17.4276C14.6148 17.8081 14.9924 18.5292 14.9924 19.6711C14.9924 21.2936 14.9727 22.5957 14.9727 22.9962C14.9727 23.3168 15.1915 23.6976 15.7879 23.5774C20.56 21.9745 23.9999 17.4475 23.9999 12.0992C24.0196 5.40833 18.6312 0 12.0099 0Z"
                            fill="#24292F" />
                    </svg>
                    {{ __('Login with Github') }}
                </x-button>
            @endif
            @if ($setting->twitter_active)
                <x-button class="grow basis-full justify-start rounded-xl sm:basis-[45%]" size="lg" variant="outline"
                    href="{{ route('login.twitter') }}">
                    <svg class="shrink-0" width="22" height="22" viewBox="0 0 24 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M21.4789 4.96354C21.4935 5.17354 21.4935 5.38354 21.4935 5.59547C21.4935 12.0532 16.5773 19.501 7.58797 19.501V19.4971C4.93249 19.501 2.33216 18.7403 0.0966797 17.3061C0.482809 17.3526 0.870873 17.3758 1.25991 17.3768C3.46055 17.3787 5.59829 16.6403 7.32958 15.2806C5.23829 15.241 3.40442 13.8774 2.76378 11.8868C3.49636 12.0281 4.2512 11.999 4.97023 11.8026C2.69023 11.3419 1.04991 9.3387 1.04991 7.01225C1.04991 6.99096 1.04991 6.97064 1.04991 6.95031C1.72926 7.3287 2.48991 7.5387 3.26797 7.56193C1.12055 6.12676 0.458615 3.26999 1.75539 1.03644C4.23668 4.08967 7.89765 5.9458 11.8276 6.14225C11.4338 4.44483 11.9718 2.66612 13.2415 1.47289C15.2099 -0.377429 18.3057 -0.28259 20.156 1.68483C21.2505 1.46902 22.2996 1.06741 23.2596 0.498377C22.8947 1.62967 22.1312 2.59064 21.1112 3.20128C22.0799 3.08709 23.0264 2.82773 23.9176 2.43193C23.2615 3.41515 22.4351 4.2716 21.4789 4.96354Z"
                            fill="#1D9BF0" />
                    </svg>
                    {{ __('Login with Twitter') }}
                </x-button>
            @endif
            @if ($setting->facebook_active)
                <x-button class="grow basis-full justify-start rounded-xl sm:basis-[45%]" size="lg" variant="outline"
                    href="{{ route('login.facebook') }}">
                    <svg class="shrink-0" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M24 12C24 5.3726 18.6274 2.65179e-05 12 2.65179e-05C5.37258 2.65179e-05 0 5.3726 0 12C0 17.9896 4.38823 22.954 10.125 23.8542V15.4688H7.07813V12H10.125V9.35628C10.125 6.34878 11.9165 4.68753 14.6576 4.68753C15.9705 4.68753 17.3438 4.9219 17.3438 4.9219V7.87503H15.8306C14.3399 7.87503 13.875 8.80003 13.875 9.74902V12H17.2031L16.6711 15.4688H13.875V23.8542C19.6118 22.954 24 17.9896 24 12Z"
                            fill="#1877F2" />
                        <path
                            d="M16.6711 15.4687L17.2031 12H13.875V9.74899C13.875 8.80001 14.3399 7.875 15.8306 7.875H17.3438V4.92187C17.3438 4.92187 15.9705 4.6875 14.6576 4.6875C11.9165 4.6875 10.125 6.34875 10.125 9.35625V12H7.07812V15.4687H10.125V23.8542C10.7359 23.9501 11.3621 24 12 24C12.6379 24 13.2641 23.9501 13.875 23.8542V15.4687H16.6711Z"
                            fill="white" />
                    </svg>
                    {{ __('Login with Facebook') }}
                </x-button>
            @endif
        </div>
        <div class="my-6 flex items-center gap-8 text-black text-opacity-60 dark:text-white dark:text-opacity-60">
            <span class="inline-block h-px grow bg-foreground/10"></span>
            {{ __('or') }}
            <span class="inline-block h-px grow bg-foreground/10"></span>
        </div>
    @endif
    <form class="flex flex-col gap-6" novalidate="novalidate" onsubmit="return LoginForm();">
        <input id="plan" type="hidden" value="{{ $plan }}" name="plan">

        <x-forms.input id="email" type="email" placeholder="{{ __('your@email.com') }}"
            value="{{ $app_is_demo ? 'admin@admin.com' : '' }}" label="{{ __('Email Address') }}" size="lg" />
        <x-forms.input id="password" type="password" placeholder="{{ __('Your password') }}"
            value="{{ $app_is_demo ? '12345678' : '' }}" label="{{ __('Password') }}" size="lg" />
        <div class="my-2 flex justify-between gap-2">
            <div class="grow">
                <x-forms.input id="remember" name="remember" type="checkbox" label="{{ __('Remember me') }}" />
            </div>
            <div class="ms-auto text-end">
                <a class="text-indigo-600" href="{{ route('forgot_password') }}">{{ __('Forgot Password?') }}</a>
            </div>
        </div>

        @if($setting->recaptcha_login && config('services.recaptcha.key') && config('services.recaptcha.secret'))
            <!-- Google Recaptcha Widget-->
            <div class="g-recaptcha mt-4" data-sitekey="{{config('services.recaptcha.key')}}"></div>
            <input class="hidden" id="recaptcha" value="1">
        @else
            <input class="hidden" id="recaptcha" value="0">
        @endif

        <x-button class="text-sm" id="LoginFormButton" size="lg" type="submit" tag="button">
            {{ __('Sign in') }}
        </x-button>
        @if ($setting->privacy_enable == 1 && $setting->privacy_enable_login == 1)
            <div class="text-foreground/70">
                {{ __('By proceeding, you acknowledge and accept our') }}
                <a class="font-medium text-foreground underline" href="{{ url('/terms') }}" target="_blank">
                    {{ __('Terms and Conditions') }}
                </a>
                {{ __('and') }}
                <a class="font-medium text-foreground underline" href="{{ url('/privacy-policy') }}" target="_blank">
                    {{ __('Privacy Policy') }}
                </a>.
            </div>
        @endif
        @if ($app_is_demo)
            <hr class="my-7">
            <x-card class="text-start" size="none">
                <x-table>
                    <x-slot:head>
                        <tr>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Password') }}</th>
                        </tr>
                    </x-slot:head>
                    <x-slot:body class="font-medium">
                        <tr>
                            <td>{{ __('User') }}</td>
                            <td>user@user.com</td>
                            <td>12345678</td>
                        </tr>
                    </x-slot:body>
                </x-table>
            </x-card>
        @endif
    </form>
    @if ($setting->register_active == 1)
        <div class="mt-20">
            {{ __("Don't have account yet?") }}
            <a class="font-medium text-indigo-600 underline" href="{{ route('register', ['plan' => $plan]) }}">
                {{ __('Sign up') }}
            </a>
        </div>
    @endif
@endsection

@push('script')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush