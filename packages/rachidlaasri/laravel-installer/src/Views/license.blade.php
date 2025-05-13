@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.welcome.templateTitle') }}
@endsection

@section('title')
    {{ trans('installer_messages.welcome.title') }}
@endsection

@section('container')
    <div class="mx-auto w-4/5">
        <h4 class="mb-20 mt-0 font-body text-[26px]">{{ __('Welcome') }}</h4>
        <p class="mb-9 text-[75px] leading-none">🪄</p>
        <h1 class="mb-10 mt-0 font-body text-[45px]">{{ __('Let’s start.') }}</h1>
        <p class="mb-20 text-[20px]">{{ __('Thanks for purchasing MagicAI! You can now install your product in seconds and unlock the magic of AI. Let’s get you started!') }}</p>
        <p class="mb-20 text-[20px]">
            {{ __('Need help?') }}
            <a
                    class="inline-flex items-center gap-1"
                    href="https://codecanyon.net/item/magicai-openai-content-text-image-chat-code-generator-as-saas/45408109"
                    target="_blank"
            >
                {{ __('See how it works') }}
                <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                >
                    <mask
                            id="mask0_0_24"
                            style="mask-type:alpha"
                            maskUnits="userSpaceOnUse"
                            x="0"
                            y="0"
                            width="24"
                            height="24"
                    >
                        <rect
                                width="24"
                                height="24"
                                fill="#D9D9D9"
                        />
                    </mask>
                    <g mask="url(#mask0_0_24)">
                        <path
                                d="M9.5 16.5L16.5 12L9.5 7.5V16.5ZM12 22C10.6167 22 9.31667 21.7375 8.1 21.2125C6.88333 20.6875 5.825 19.975 4.925 19.075C4.025 18.175 3.3125 17.1167 2.7875 15.9C2.2625 14.6833 2 13.3833 2 12C2 10.6167 2.2625 9.31667 2.7875 8.1C3.3125 6.88333 4.025 5.825 4.925 4.925C5.825 4.025 6.88333 3.3125 8.1 2.7875C9.31667 2.2625 10.6167 2 12 2C13.3833 2 14.6833 2.2625 15.9 2.7875C17.1167 3.3125 18.175 4.025 19.075 4.925C19.975 5.825 20.6875 6.88333 21.2125 8.1C21.7375 9.31667 22 10.6167 22 12C22 13.3833 21.7375 14.6833 21.2125 15.9C20.6875 17.1167 19.975 18.175 19.075 19.075C18.175 19.975 17.1167 20.6875 15.9 21.2125C14.6833 21.7375 13.3833 22 12 22ZM12 20C14.2333 20 16.125 19.225 17.675 17.675C19.225 16.125 20 14.2333 20 12C20 9.76667 19.225 7.875 17.675 6.325C16.125 4.775 14.2333 4 12 4C9.76667 4 7.875 4.775 6.325 6.325C4.775 7.875 4 9.76667 4 12C4 14.2333 4.775 16.125 6.325 17.675C7.875 19.225 9.76667 20 12 20Z"
                                fill="#5E2796"
                        />
                    </g>
                </svg>
            </a>
        </p>
    </div>
    <a
            class="flex items-center justify-center gap-2 rounded-xl p-2 font-medium shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-all duration-300 hover:scale-105 hover:bg-black hover:text-white"
            href="{{ route('LaravelInstaller::requirements') }}"
    >
        {{ __('Next') }}
        <svg
                xmlns="http://www.w3.org/2000/svg"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor"
                fill="none"
                stroke-linecap="round"
                stroke-linejoin="round"
        >
            <path d="M9 6l6 6l-6 6"></path>
        </svg>
    </a>
@endsection
