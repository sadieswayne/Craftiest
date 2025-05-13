@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.final.templateTitle') }}
@endsection

@section('title')
    <i
        class="fa fa-flag-checkered fa-fw"
        aria-hidden="true"
    ></i>
    {{ trans('installer_messages.final.title') }}
@endsection

@section('container')
    <div class="mx-auto w-4/5 text-center">
        <svg
            class="absolute -start-5 top-28"
            width="57"
            height="54"
            viewBox="0 0 57 54"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M0 6.00092C5.92726 22.1866 8.66341 31.535 20.9749 35.1822C33.2864 38.8293 40.3528 38.6013 46.281 53.4209C52.4373 50.9119 56.9937 49.0878 56.9937 49.0878C53.8145 42.5223 48.513 37.2215 41.947 34.0432C31.0025 28.8 23.4791 31.079 18.4659 20.5905C15.374 14.0238 13.0777 7.11141 11.6256 0L0 6.00092Z"
                fill="#D2CFDE"
            />
        </svg>

        <svg
            class="absolute -end-8 top-24"
            width="47"
            height="22"
            viewBox="0 0 47 22"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M46.7494 4.56109L36.4511 0.859852C36.3689 1.01532 32.7434 11.282 24.2796 11.4654C15.1943 11.6613 10.3772 11.8065 5.30006 15.2519C0.222912 18.6973 0.198319 19.9293 0.198319 19.9293L9.14664 21.2467C9.14664 21.2467 10.5232 16.6891 14.9654 16.8043C19.4076 16.9195 28.3567 18.785 34.1876 16.8049C41.2427 14.4068 46.7494 4.56109 46.7494 4.56109Z"
                fill="#D2CFDE"
            />
        </svg>

        <h4 class="mb-16 mt-0 font-body text-[26px]">{{ __('Done') }}!</h4>

        <svg
            class="mx-auto mb-7"
            width="95"
            height="95"
            viewBox="0 0 95 95"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M39.7061 67.3875L73.0311 34.0625L70.3436 31.375L39.7061 62.0125L24.3873 46.6937L21.6998 49.3812L39.7061 67.3875ZM47.5232 94.2625C41.0576 94.2625 34.9779 93.0356 29.2842 90.5818C23.5904 88.128 18.6375 84.7978 14.4257 80.5913C10.2139 76.3847 6.87959 71.4382 4.42267 65.7516C1.96576 60.065 0.737305 53.989 0.737305 47.5234C0.737305 41.0578 1.96419 34.9781 4.41797 29.2844C6.87184 23.5906 10.202 18.6378 14.4085 14.426C18.615 10.2141 23.5616 6.87977 29.2482 4.42286C34.9348 1.96595 41.0108 0.737488 47.4764 0.737488C53.942 0.737488 60.0217 1.96438 65.7154 4.41815C71.4092 6.87202 76.362 10.2022 80.5738 14.4087C84.7856 18.6152 88.12 23.5618 90.5769 29.2484C93.0338 34.9349 94.2623 41.011 94.2623 47.4766C94.2623 53.9422 93.0354 60.0219 90.5816 65.7156C88.1278 71.4094 84.7976 76.3623 80.5911 80.5741C76.3846 84.7859 71.438 88.1202 65.7514 90.5771C60.0649 93.034 53.9888 94.2625 47.5232 94.2625ZM47.4998 90.5C59.504 90.5 69.6717 86.3344 78.0029 78.0031C86.3342 69.6719 90.4998 59.5042 90.4998 47.5C90.4998 35.4958 86.3342 25.3281 78.0029 16.9969C69.6717 8.66561 59.504 4.49999 47.4998 4.49999C35.4956 4.49999 25.3279 8.66561 16.9967 16.9969C8.66543 25.3281 4.4998 35.4958 4.4998 47.5C4.4998 59.5042 8.66543 69.6719 16.9967 78.0031C25.3279 86.3344 35.4956 90.5 47.4998 90.5Z"
                fill="#35CB98"
            />
        </svg>

        <h1 class="mb-10 mt-0 font-body text-[45px]">{{ __('Installation Completed.') }}</h1>

        <p class="mb-28 text-[20px]">{{ __('You have completed the smart installation. You can now register your product in 10 seconds unlock exclusive features.') }}</p>
    </div>

    <a
        class="flex items-center justify-center gap-2 rounded-xl p-2 font-medium shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-all duration-300 hover:scale-105 hover:bg-black hover:text-white"
        href="{{ url('/license') }}"
    >
        {{ __('Explore MagicAI') }}
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
