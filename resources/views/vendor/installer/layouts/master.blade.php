@props([
    'stepShow' => $stepShow ?? true,
])
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    >
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >
    <title>
        @if (trim($__env->yieldContent('template_title')))
            @yield('template_title') |
        @endif {{ trans('installer_messages.title') }}
    </title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >
    <link
        href="https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="icon"
        href="/favicon.ico"
    >
    @php
        $link = 'resources/views/' . get_theme() . '/scss/landing-page.scss';
    @endphp
    @vite($link)

    @yield('style')
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>

<body class="bg-white font-body font-normal text-[#272D38] antialiased">
    @if ($stepShow)
        <div class="fixed inset-x-0 top-0 z-50 border-b border-solid border-[#f1f1f1] bg-white text-[15px] font-medium">
            <div class="container">
                <div class="grid grid-cols-4 place-content-center">
                    <div class="{{ isActive('LaravelInstaller::welcome') }} relative flex items-center gap-3 text-black opacity-40 [&.active]:opacity-100">
                        <span class="size-9 inline-flex items-center justify-center rounded-full border-2 border-solid border-current text-lg">1</span>
                        @if (Request::is('install') ||
                                Request::is('install/requirements') ||
                                Request::is('install/permissions') ||
                                Request::is('install/environment') ||
                                Request::is('install/environment/wizard') ||
                                Request::is('install/environment/classic'))
                            <a
                                class="absolute inset-0"
                                href="{{ route('LaravelInstaller::welcome') }}"
                            ></a>
                        @endif
                        {{ __('Welcome') }}
                        <svg
                            class="mx-auto h-full"
                            preserveAspectRatio="none"
                            width="22"
                            height="66"
                            viewBox="0 0 22 66"
                            fill="none"
                            stroke="#f1f1f1"
                            stroke-width="1.5"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M1 -1L21 32L1 65" />
                        </svg>
                    </div>
                    <div class="{{ isActive('LaravelInstaller::requirements') }} relative flex items-center gap-3 text-black opacity-40 [&.active]:opacity-100">
                        <span class="size-9 inline-flex items-center justify-center rounded-full border-2 border-solid border-current text-lg">2</span>
                        @if (Request::is('install') ||
                                Request::is('install/requirements') ||
                                Request::is('install/permissions') ||
                                Request::is('install/environment') ||
                                Request::is('install/environment/wizard') ||
                                Request::is('install/environment/classic'))
                            <a
                                class="absolute inset-0"
                                href="{{ route('LaravelInstaller::requirements') }}"
                            ></a>
                        @endif
                        {{ __('Server Requirements') }}
                        <svg
                            class="mx-auto h-full"
                            preserveAspectRatio="none"
                            width="22"
                            height="66"
                            viewBox="0 0 22 66"
                            fill="none"
                            stroke="#f1f1f1"
                            stroke-width="1.5"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M1 -1L21 32L1 65" />
                        </svg>
                    </div>
                    <div
                        class="{{ isActive('LaravelInstaller::environment') }} {{ isActive('LaravelInstaller::environmentWizard') }} {{ isActive('LaravelInstaller::environmentClassic') }} relative flex items-center gap-3 text-black opacity-40 [&.active]:opacity-100">
                        <span class="size-9 inline-flex items-center justify-center rounded-full border-2 border-solid border-current text-lg">3</span>
                        @if (Request::is('install/environment') || Request::is('install/environment/wizard') || Request::is('install/environment/classic'))
                            <a
                                class="absolute inset-0"
                                href="{{ route('LaravelInstaller::environment') }}"
                            ></a>
                        @endif
                        {{ __('Database Setup') }}
                        <svg
                            class="mx-auto h-full"
                            preserveAspectRatio="none"
                            width="22"
                            height="66"
                            viewBox="0 0 22 66"
                            fill="none"
                            stroke="#f1f1f1"
                            stroke-width="1.5"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M1 -1L21 32L1 65" />
                        </svg>
                    </div>
                    <div class="{{ isActive('LaravelInstaller::final') }} relative flex items-center gap-3 text-black opacity-40 [&.active]:opacity-100">
                        <span class="size-9 inline-flex items-center justify-center rounded-full border-2 border-solid border-current text-lg">4</span>
                        {{ __('Done') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (view()->hasSection('content'))
        @yield('content')
    @else
        <div class="container py-24">
            <div class="text-center">
                <svg
                    class="mx-auto mb-9 mix-blend-luminosity"
                    width="54"
                    height="54"
                    viewBox="0 0 54 54"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M23.1975 34.5546L23.7743 34.4375C23.9358 34.4051 24.0811 34.3178 24.1856 34.1904C24.29 34.0629 24.347 33.9032 24.347 33.7385C24.347 33.5737 24.29 33.414 24.1856 33.2866C24.0811 33.1592 23.9358 33.0719 23.7743 33.0395L23.1975 32.9223C22.4862 32.778 21.8333 32.4274 21.3201 31.9143C20.807 31.4011 20.4563 30.7482 20.3121 30.037L20.1949 29.4601C20.1626 29.2986 20.0752 29.1532 19.9478 29.0488C19.8204 28.9444 19.6607 28.8874 19.4959 28.8874C19.3312 28.8874 19.1715 28.9444 19.0441 29.0488C18.9166 29.1532 18.8293 29.2986 18.7969 29.4601L18.6798 30.037C18.5355 30.7482 18.1849 31.4011 17.6717 31.9143C17.1585 32.4274 16.5056 32.778 15.7944 32.9223L15.2176 33.0395C15.056 33.0719 14.9107 33.1592 14.8063 33.2866C14.7018 33.414 14.6448 33.5737 14.6448 33.7385C14.6448 33.9032 14.7018 34.0629 14.8063 34.1904C14.9107 34.3178 15.056 34.4051 15.2176 34.4375L15.7944 34.5546C16.5056 34.6989 17.1585 35.0495 17.6717 35.5627C18.1849 36.0759 18.5355 36.7288 18.6798 37.44L18.7969 38.0168C18.8293 38.1784 18.9166 38.3237 19.0441 38.4281C19.1715 38.5326 19.3312 38.5896 19.4959 38.5896C19.6607 38.5896 19.8204 38.5326 19.9478 38.4281C20.0752 38.3237 20.1626 38.1784 20.1949 38.0168L20.3121 37.44C20.4563 36.7288 20.807 36.0759 21.3201 35.5627C21.8333 35.0495 22.4862 34.6989 23.1975 34.5546Z"
                        fill="url(#paint0_linear_0_247)"
                    />
                    <path
                        d="M37.178 24.1095L39.3007 23.6791C39.5125 23.6358 39.7029 23.5206 39.8396 23.3531C39.9763 23.1856 40.051 22.976 40.051 22.7597C40.051 22.5435 39.9763 22.3339 39.8396 22.1664C39.7029 21.9989 39.5125 21.8837 39.3007 21.8404L37.178 21.41C36.1554 21.2025 35.2166 20.6983 34.4787 19.9605C33.7409 19.2227 33.2367 18.2838 33.0292 17.2612L32.5988 15.1385C32.5562 14.9262 32.4414 14.7353 32.274 14.5981C32.1065 14.4608 31.8967 14.3859 31.6802 14.3859C31.4637 14.3859 31.2539 14.4608 31.0864 14.5981C30.9189 14.7353 30.8042 14.9262 30.7616 15.1385L30.3312 17.2612C30.1238 18.2839 29.6198 19.2228 28.8819 19.9607C28.144 20.6986 27.2051 21.2026 26.1824 21.41L24.0597 21.8404C23.8479 21.8837 23.6575 21.9989 23.5207 22.1664C23.384 22.3339 23.3093 22.5435 23.3093 22.7597C23.3093 22.976 23.384 23.1856 23.5207 23.3531C23.6575 23.5206 23.8479 23.6358 24.0597 23.6791L26.1824 24.1095C27.2051 24.3168 28.144 24.8209 28.8819 25.5588C29.6198 26.2966 30.1238 27.2356 30.3312 28.2583L30.7616 30.381C30.8042 30.5932 30.9189 30.7842 31.0864 30.9214C31.2539 31.0586 31.4637 31.1336 31.6802 31.1336C31.8967 31.1336 32.1065 31.0586 32.274 30.9214C32.4414 30.7842 32.5562 30.5932 32.5988 30.381L33.0292 28.2583C33.2367 27.2357 33.7409 26.2968 34.4787 25.559C35.2166 24.8212 36.1554 24.317 37.178 24.1095Z"
                        fill="url(#paint1_linear_0_247)"
                    />
                    <defs>
                        <linearGradient
                            id="paint0_linear_0_247"
                            x1="24.347"
                            y1="33.7385"
                            x2="14.5381"
                            y2="32.6615"
                            gradientUnits="userSpaceOnUse"
                        >
                            <stop stop-color="#8D65E9" />
                            <stop
                                offset="0.483"
                                stop-color="#5391E4"
                            />
                            <stop
                                offset="1"
                                stop-color="#6BCD94"
                            />
                        </linearGradient>
                        <linearGradient
                            id="paint1_linear_0_247"
                            x1="40.051"
                            y1="22.7597"
                            x2="23.125"
                            y2="20.9021"
                            gradientUnits="userSpaceOnUse"
                        >
                            <stop stop-color="#8D65E9" />
                            <stop
                                offset="0.483"
                                stop-color="#5391E4"
                            />
                            <stop
                                offset="1"
                                stop-color="#6BCD94"
                            />
                        </linearGradient>
                    </defs>
                </svg>
                @if (session('message') || session()->has('errors'))
                    <div class="relative mx-auto w-1/2 px-10 text-start">
                        <div class="mb-8 mt-3 flex flex-col">
                            @if (session('message'))
                                <p class="alert text-center">
                                    <strong>
                                        @if (is_array(session('message')))
                                            {{ session('message')['message'] }}
                                        @else
                                            {{ session('message') }}
                                        @endif
                                    </strong>
                                </p>
                            @endif
                            @if (session()->has('errors'))
                                <div
                                    class="relative z-10 w-full rounded-lg bg-red-100 p-5 text-sm font-medium"
                                    id="error_alert"
                                >
                                    <button
                                        class="close size-8 absolute -end-4 -top-4 flex items-center justify-center rounded-full bg-red-900 text-white"
                                        id="close_alert"
                                        data-dismiss="alert"
                                        type="button"
                                        aria-hidden="true"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="20"
                                            height="20"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="M18 6l-12 12"></path>
                                            <path d="M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <h4 class="mb-2 flex items-center">
                                        <svg
                                            class="me-2 text-red-600"
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                            <path d="M12 9v4"></path>
                                            <path d="M12 16v.01"></path>
                                        </svg>
                                        {{ trans('installer_messages.forms.errorTitle') }}
                                    </h4>
                                    <ol class="ms-[24px] list-inside list-decimal ps-2">
                                        @foreach ($errors->all() as $error)
                                            <li class="mb-[2px] last:mb-0">{{ $error }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="relative mx-auto w-1/2">
                    <svg
                        class="absolute -end-2/3 -top-44 z-0"
                        width="686"
                        height="694"
                        viewBox="0 0 686 694"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <g filter="url(#filter0_f_0_6)">
                            <path
                                d="M337.772 452.362C321.525 457.538 304.359 459.177 287.427 457.168C270.494 455.16 254.188 449.552 239.602 440.72C225.017 431.888 212.49 420.037 202.864 405.963C193.238 391.889 186.736 375.918 183.793 359.123C180.85 342.328 181.536 325.097 185.804 308.589C190.072 292.081 197.823 276.677 208.537 263.412C219.251 250.148 232.68 239.33 247.921 231.685C263.162 224.04 279.863 219.745 296.901 219.089L301.5 338.5L337.772 452.362Z"
                                fill="#4B47FF"
                                fill-opacity="0.22"
                            />
                        </g>
                        <g filter="url(#filter1_f_0_6)">
                            <path
                                d="M246 378.727C253.599 363.463 264.376 350.001 277.608 339.246C290.839 328.492 306.219 320.694 322.714 316.375C339.21 312.057 356.438 311.318 373.242 314.21C390.046 317.101 406.037 323.555 420.14 333.138C434.244 342.721 446.133 355.211 455.009 369.769C463.886 384.328 469.544 400.617 471.604 417.543C473.664 434.469 472.078 451.64 466.952 467.903C461.826 484.165 453.279 499.142 441.886 511.828L352.979 431.979L246 378.727Z"
                                fill="#0044F1"
                                fill-opacity="0.11"
                            />
                        </g>
                        <g filter="url(#filter2_f_0_6)">
                            <path
                                d="M277 233.727C284.599 218.463 295.376 205.001 308.608 194.246C321.839 183.492 337.219 175.694 353.714 171.375C370.21 167.057 387.438 166.318 404.242 169.21C421.046 172.101 437.037 178.555 451.14 188.138C465.244 197.721 477.133 210.211 486.009 224.769C494.886 239.328 500.544 255.617 502.604 272.543C504.664 289.469 503.078 306.64 497.952 322.903C492.826 339.165 484.279 354.142 472.886 366.828L383.979 286.979L277 233.727Z"
                                fill="#8700F1"
                                fill-opacity="0.11"
                            />
                        </g>
                        <defs>
                            <filter
                                id="filter0_f_0_6"
                                x="0"
                                y="37.0885"
                                width="519.772"
                                height="602.912"
                                filterUnits="userSpaceOnUse"
                                color-interpolation-filters="sRGB"
                            >
                                <feFlood
                                    flood-opacity="0"
                                    result="BackgroundImageFix"
                                />
                                <feBlend
                                    mode="normal"
                                    in="SourceGraphic"
                                    in2="BackgroundImageFix"
                                    result="shape"
                                />
                                <feGaussianBlur
                                    stdDeviation="70"
                                    result="effect1_foregroundBlur_0_6"
                                />
                            </filter>
                            <filter
                                id="filter1_f_0_6"
                                x="64.0004"
                                y="130.479"
                                width="590.479"
                                height="563.349"
                                filterUnits="userSpaceOnUse"
                                color-interpolation-filters="sRGB"
                            >
                                <feFlood
                                    flood-opacity="0"
                                    result="BackgroundImageFix"
                                />
                                <feBlend
                                    mode="normal"
                                    in="SourceGraphic"
                                    in2="BackgroundImageFix"
                                    result="shape"
                                />
                                <feGaussianBlur
                                    stdDeviation="91"
                                    result="effect1_foregroundBlur_0_6"
                                />
                            </filter>
                            <filter
                                id="filter2_f_0_6"
                                x="95.0004"
                                y="-14.5208"
                                width="590.479"
                                height="563.349"
                                filterUnits="userSpaceOnUse"
                                color-interpolation-filters="sRGB"
                            >
                                <feFlood
                                    flood-opacity="0"
                                    result="BackgroundImageFix"
                                />
                                <feBlend
                                    mode="normal"
                                    in="SourceGraphic"
                                    in2="BackgroundImageFix"
                                    result="shape"
                                />
                                <feGaussianBlur
                                    stdDeviation="91"
                                    result="effect1_foregroundBlur_0_6"
                                />
                            </filter>
                        </defs>
                    </svg>
                    <div class="relative rounded-2xl px-10 py-8 shadow-[0_4px_20px_rgba(0,0,0,0.04)] backdrop-blur-md backdrop-saturate-150">
                        @yield('container')
                    </div>
                    <?php
                    $back_href = '';
                    if (Request::is('install/requirements')) {
                        $back_href = route('LaravelInstaller::welcome');
                    }
                    if (Request::is('install/environment/wizard')) {
                        $back_href = route('LaravelInstaller::requirements');
                    }
                    ?>
                    @if (!empty($back_href))
                        <div class="mt-8 text-center">
                            <a
                                class="flex items-center justify-center gap-2 opacity-70 transition-opacity hover:opacity-100"
                                href="{{ $back_href }}"
                            >
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
                                    <path d="M15 6l-6 6l6 6"></path>
                                </svg>
                                {{ __('Back') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="fixed bottom-10 end-10">
        <a
            class="size-10 inline-flex items-center justify-center rounded-full border border-black border-opacity-10 transition-all hover:border-black hover:bg-black hover:text-white"
            target="_blank"
            href="https://magicaidocs.liquid-themes.com/"
        >
            <svg
                class="icon icon-tabler icon-tabler-question-mark"
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                fill="none"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M8 8a3.5 3 0 0 1 3.5 -3h1a3.5 3 0 0 1 3.5 3a3 3 0 0 1 -2 3a3 4 0 0 0 -2 4"></path>
                <path d="M12 19l0 .01"></path>
            </svg>
        </a>
    </div>
    @yield('scripts')
    <script type="text/javascript">
        var errorAlert = document.getElementById('error_alert');
        var closeAlert = document.getElementById('close_alert');
        if (closeAlert) {
            closeAlert.onclick = function() {
                errorAlert.style.display = "none";
            };
        }
    </script>
</body>

</html>
