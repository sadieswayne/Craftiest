@extends('panel.layout.settings')
@section('title', __('General Settings'))
@section('titlebar_actions', '')
@section('additional_css')

@endsection

@section('settings')

    <h2 class="mb-4">
        @lang('Preferences')
    </h2>
    <p class="mb-8">
        @lang('Effortlessly manage your script preferences, enhance your branding, optimize your use of AI tools, and much more with MagicAI’s advanced settings.')
    </p>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2">

        <a href="{{ route('dashboard.admin.config.branding.index') }}">
            <div
                class="flex items-center gap-1 rounded-xl p-5 shadow-md shadow-black/5 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-black/[7%] dark:bg-heading-foreground/[2%]">
                <div>
                    <h3 class="mb-0 text-2xs text-current">{{__("Branding")}}</h3>
                    <p class="m-0 text-2xs text-current opacity-80">{{__("Site Name and Logo.")}}</p>
                </div>
                <div class="ms-auto">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="40"
                        height="40"
                        viewBox="0 0 40 40"
                        fill="none"
                    >
                        <g clip-path="url(#clip0_6446_1455)">
                            <path
                                d="M5 35V28.3334C5 27.0148 5.39099 25.7259 6.12354 24.6296C6.85608 23.5332 7.89727 22.6787 9.11544 22.1742C10.3336 21.6696 11.6741 21.5376 12.9673 21.7948C14.2605 22.052 15.4484 22.687 16.3807 23.6193C17.3131 24.5517 17.948 25.7395 18.2052 27.0328C18.4625 28.326 18.3304 29.6664 17.8259 30.8846C17.3213 32.1028 16.4668 33.1439 15.3705 33.8765C14.2741 34.609 12.9852 35 11.6667 35H5Z"
                                stroke="url(#paint0_linear_6446_1455)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M35.0001 5C30.2431 5.65067 25.75 7.57357 21.9952 10.5657C18.2403 13.5579 15.3629 17.5083 13.6667 22"
                                stroke="url(#paint1_linear_6446_1455)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M35 5C34.3493 9.75697 32.4264 14.25 29.4343 18.0049C26.4421 21.7598 22.4917 24.6372 18 26.3333"
                                stroke="url(#paint2_linear_6446_1455)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M17.6667 15C20.9054 16.4948 23.5053 19.0947 25.0001 22.3333"
                                stroke="url(#paint3_linear_6446_1455)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </g>
                        <defs>
                            <linearGradient
                                id="paint0_linear_6446_1455"
                                x1="5"
                                y1="24.3867"
                                x2="16.1867"
                                y2="34.2534"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint1_linear_6446_1455"
                                x1="13.6667"
                                y1="8.468"
                                x2="27.9686"
                                y2="24.2977"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint2_linear_6446_1455"
                                x1="18"
                                y1="9.352"
                                x2="34.9737"
                                y2="21.2819"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint3_linear_6446_1455"
                                x1="17.6667"
                                y1="16.496"
                                x2="23.8194"
                                y2="21.9227"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <clipPath id="clip0_6446_1455">
                                <rect
                                    width="40"
                                    height="40"
                                    fill="white"
                                />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard.admin.config.ai-tools.index') }}">
            <div
                class="flex items-center gap-1 rounded-xl p-5 shadow-md shadow-black/5 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-black/[7%] dark:bg-heading-foreground/[2%]">
                <div>
                    <h3 class="mb-0 text-2xs text-current">AI Tools</h3>
                    <p class="m-0 text-2xs text-current opacity-80">Manage AI Tools</p>
                </div>
                <div class="ms-auto">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="40"
                        height="40"
                        viewBox="0 0 40 40"
                        fill="none"
                    >
                        <g clip-path="url(#clip0_6446_1475)">
                            <path
                                d="M18.6951 32.3066C19.1714 33.5894 20.0834 34.6639 21.2716 35.3424C22.4598 36.0209 23.8487 36.2602 25.1955 36.0186C26.5423 35.777 27.7614 35.0697 28.6396 34.0204C29.5178 32.9712 29.9994 31.6466 30.0001 30.2783V21.6666L20.0001 16.0833"
                                stroke="url(#paint0_linear_6446_1475)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M8.68996 25.0233C7.81657 26.0772 7.34146 27.4046 7.34772 28.7733C7.35397 30.142 7.8412 31.465 8.72419 32.5108C9.60718 33.5567 10.8297 34.2588 12.178 34.4945C13.5263 34.7302 14.9146 34.4843 16.1 33.8L23.3333 29.5767V18"
                                stroke="url(#paint1_linear_6446_1475)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M10 12.7167C7.68169 12.3233 5.35503 13.375 4.11003 15.5317C3.34245 16.8613 3.13441 18.4414 3.53165 19.9245C3.9289 21.4075 4.89891 22.6721 6.22836 23.44L13.3334 27.63L23.3334 22.05"
                                stroke="url(#paint2_linear_6446_1475)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M21.305 7.69333C20.8287 6.41062 19.9167 5.3361 18.7284 4.65759C17.5402 3.97908 16.1513 3.73973 14.8045 3.98137C13.4577 4.22301 12.2387 4.93027 11.3605 5.97955C10.4823 7.02882 10.0007 8.35336 10 9.72166V18.1667L20 23.9167"
                                stroke="url(#paint3_linear_6446_1475)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M31.3101 14.9766C32.1835 13.9228 32.6586 12.5954 32.6523 11.2267C32.6461 9.85795 32.1588 8.53498 31.2759 7.48913C30.3929 6.44329 29.1703 5.74114 27.822 5.50548C26.4737 5.26982 25.0855 5.51565 23.9001 6.19998L16.6667 10.4233V22"
                                stroke="url(#paint4_linear_6446_1475)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M30.0001 27.17C32.3184 27.5634 34.6451 26.5117 35.8901 24.355C36.6577 23.0254 36.8657 21.4453 36.4685 19.9622C36.0712 18.4792 35.1012 17.2147 33.7718 16.4467L26.5917 12.2567L16.6667 17.9567"
                                stroke="url(#paint5_linear_6446_1475)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </g>
                        <defs>
                            <linearGradient
                                id="paint0_linear_6446_1475"
                                x1="18.6951"
                                y1="20.1688"
                                x2="32.2087"
                                y2="26.897"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint1_linear_6446_1475"
                                x1="7.34766"
                                y1="21.3827"
                                x2="21.1871"
                                y2="33.1503"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint2_linear_6446_1475"
                                x1="3.33447"
                                y1="15.6944"
                                x2="15.8487"
                                y2="30.4157"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint3_linear_6446_1475"
                                x1="10"
                                y1="7.97516"
                                x2="23.5136"
                                y2="14.7033"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint4_linear_6446_1475"
                                x1="16.6667"
                                y1="8.80099"
                                x2="30.5062"
                                y2="20.5687"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint5_linear_6446_1475"
                                x1="16.6667"
                                y1="15.3156"
                                x2="29.181"
                                y2="30.037"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <clipPath id="clip0_6446_1475">
                                <rect
                                    width="40"
                                    height="40"
                                    fill="white"
                                />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('elseyyid.translations.home') }}">
            <div
                class="flex items-center gap-1 rounded-xl p-5 shadow-md shadow-black/5 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-black/[7%] dark:bg-heading-foreground/[2%]">
                <div>
                    <h3 class="mb-0 text-2xs text-current">{{__("Languages")}}</h3>
                    <p class="m-0 text-2xs text-current opacity-80">{{__("String Translation.")}}</p>
                </div>
                <div class="ms-auto">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="40"
                        height="40"
                        viewBox="0 0 40 40"
                        fill="none"
                    >
                        <g clip-path="url(#clip0_6446_1529)">
                            <path
                                d="M5 20C5 21.9698 5.38799 23.9204 6.14181 25.7403C6.89563 27.5601 8.00052 29.2137 9.3934 30.6066C10.7863 31.9995 12.4399 33.1044 14.2597 33.8582C16.0796 34.612 18.0302 35 20 35C21.9698 35 23.9204 34.612 25.7403 33.8582C27.5601 33.1044 29.2137 31.9995 30.6066 30.6066C31.9995 29.2137 33.1044 27.5601 33.8582 25.7403C34.612 23.9204 35 21.9698 35 20C35 16.0218 33.4196 12.2064 30.6066 9.3934C27.7936 6.58035 23.9782 5 20 5C16.0218 5 12.2064 6.58035 9.3934 9.3934C6.58035 12.2064 5 16.0218 5 20Z"
                                stroke="url(#paint0_linear_6446_1529)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M6 15H34"
                                stroke="url(#paint1_linear_6446_1529)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M6 25H34"
                                stroke="url(#paint2_linear_6446_1529)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M19.1667 5C16.3589 9.49935 14.8704 14.6964 14.8704 20C14.8704 25.3036 16.3589 30.5006 19.1667 35"
                                stroke="url(#paint3_linear_6446_1529)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M20.8333 5C23.641 9.49935 25.1296 14.6964 25.1296 20C25.1296 25.3036 23.641 30.5006 20.8333 35"
                                stroke="url(#paint4_linear_6446_1529)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </g>
                        <defs>
                            <linearGradient
                                id="paint0_linear_6446_1529"
                                x1="5"
                                y1="11.12"
                                x2="30.17"
                                y2="33.32"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint1_linear_6446_1529"
                                x1="6"
                                y1="15.204"
                                x2="6.06837"
                                y2="16.8925"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint2_linear_6446_1529"
                                x1="6"
                                y1="25.204"
                                x2="6.06837"
                                y2="26.8925"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint3_linear_6446_1529"
                                x1="14.8704"
                                y1="11.12"
                                x2="21.1785"
                                y2="11.9168"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint4_linear_6446_1529"
                                x1="20.8333"
                                y1="11.12"
                                x2="27.1414"
                                y2="11.9168"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <clipPath id="clip0_6446_1529">
                                <rect
                                    width="40"
                                    height="40"
                                    fill="white"
                                />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard.admin.config.storage.index') }}">
            <div
                class="flex items-center gap-1 rounded-xl p-5 shadow-md shadow-black/5 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-black/[7%] dark:bg-heading-foreground/[2%]">
                <div>
                    <h3 class="mb-0 text-2xs text-current">{{__("Storage")}}</h3>
                    <p class="m-0 text-2xs text-current opacity-80">{{__("Primary Storage")}}</p>
                </div>
                <div class="ms-auto">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="40"
                        height="40"
                        viewBox="0 0 40 40"
                        fill="none"
                    >
                        <g clip-path="url(#clip0_6446_1536)">
                            <path
                                d="M11.0949 26.6667C6.80825 26.6667 3.33325 23.3217 3.33325 19.195C3.33325 15.07 6.80825 11.725 11.0949 11.725C11.7499 8.78832 14.0849 6.39166 17.2199 5.43666C20.3533 4.48332 23.8133 5.11499 26.2933 7.10332C28.7733 9.08666 29.8966 12.115 29.2433 15.0517H30.8933C34.0816 15.0517 36.6666 17.6517 36.6666 20.8617C36.6666 24.0733 34.0816 26.6733 30.8916 26.6733H11.0949"
                                stroke="url(#paint0_linear_6446_1536)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M20 26.6667V35"
                                stroke="url(#paint1_linear_6446_1536)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M26.6667 26.6667V33.3334C26.6667 33.7754 26.8423 34.1993 27.1549 34.5119C27.4675 34.8244 27.8914 35 28.3334 35H35.0001"
                                stroke="url(#paint2_linear_6446_1536)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M13.3333 26.6667V33.3334C13.3333 33.7754 13.1577 34.1993 12.8452 34.5119C12.5326 34.8244 12.1087 35 11.6667 35H5"
                                stroke="url(#paint3_linear_6446_1536)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </g>
                        <defs>
                            <linearGradient
                                id="paint0_linear_6446_1536"
                                x1="3.33325"
                                y1="9.4215"
                                x2="20.8403"
                                y2="33.1702"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint1_linear_6446_1536"
                                x1="20"
                                y1="28.3667"
                                x2="21.4752"
                                y2="28.5228"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint2_linear_6446_1536"
                                x1="26.6667"
                                y1="28.3667"
                                x2="33.6584"
                                y2="34.5334"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint3_linear_6446_1536"
                                x1="5"
                                y1="28.3667"
                                x2="11.9917"
                                y2="34.5334"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <clipPath id="clip0_6446_1536">
                                <rect
                                    width="40"
                                    height="40"
                                    fill="white"
                                />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard.admin.config.seo.index') }}">
            <div
                class="flex items-center gap-1 rounded-xl p-5 shadow-md shadow-black/5 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-black/[7%] dark:bg-heading-foreground/[2%]">
                <div>
                    <h3 class="mb-0 text-2xs text-current">{{__("SEO Settings")}}</h3>
                    <p class="m-0 text-2xs text-current opacity-80">{{__("Marketing and Tracking")}}</p>
                </div>
                <div class="ms-auto">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="40"
                        height="40"
                        viewBox="0 0 40 40"
                        fill="none"
                    >
                        <g clip-path="url(#clip0_6446_1492)">
                            <path
                                d="M19.41 34.9417C18.874 34.8404 18.3796 34.5839 17.9882 34.2041C17.5968 33.8242 17.3256 33.3377 17.2083 32.805C17.1018 32.3653 16.893 31.957 16.5988 31.6133C16.3047 31.2696 15.9335 31.0002 15.5156 30.8271C15.0976 30.6539 14.6447 30.5819 14.1937 30.6169C13.7427 30.6519 13.3063 30.7928 12.92 31.0283C10.3483 32.595 7.40333 29.6517 8.97 27.0783C9.20517 26.6922 9.34592 26.2561 9.38081 25.8054C9.41569 25.3547 9.34374 24.9021 9.17079 24.4844C8.99783 24.0667 8.72877 23.6957 8.38545 23.4016C8.04214 23.1075 7.63427 22.8985 7.195 22.7917C4.26833 22.0817 4.26833 17.9183 7.195 17.2083C7.63467 17.1018 8.04296 16.893 8.38667 16.5988C8.73037 16.3047 8.99976 15.9335 9.17291 15.5156C9.34606 15.0976 9.41808 14.6447 9.38311 14.1937C9.34814 13.7427 9.20717 13.3063 8.97167 12.92C7.405 10.3483 10.3483 7.40333 12.9217 8.97C14.5883 9.98333 16.7483 9.08667 17.2083 7.195C17.9183 4.26833 22.0817 4.26833 22.7917 7.195C22.8982 7.63467 23.107 8.04297 23.4012 8.38667C23.6953 8.73037 24.0665 8.99976 24.4844 9.17291C24.9024 9.34606 25.3553 9.41809 25.8063 9.38312C26.2573 9.34815 26.6937 9.20717 27.08 8.97167C29.6517 7.405 32.5967 10.3483 31.03 12.9217C30.7948 13.3078 30.6541 13.7439 30.6192 14.1946C30.5843 14.6453 30.6563 15.0979 30.8292 15.5156C31.0022 15.9333 31.2712 16.3043 31.6145 16.5984C31.9579 16.8925 32.3657 17.1015 32.805 17.2083C34.0183 17.5033 34.7283 18.3917 34.9367 19.38"
                                stroke="url(#paint0_linear_6446_1492)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M24.975 19.49C24.8787 18.5494 24.5176 17.6554 23.9338 16.9117C23.3499 16.1681 22.5671 15.6052 21.6763 15.2884C20.7855 14.9716 19.8231 14.9138 18.9008 15.1219C17.9784 15.3299 17.134 15.7952 16.4654 16.4638C15.7969 17.1324 15.3316 17.9768 15.1235 18.8991C14.9155 19.8214 14.9732 20.7838 15.29 21.6747C15.6068 22.5655 16.1697 23.3483 16.9134 23.9321C17.6571 24.516 18.5511 24.877 19.4916 24.9733"
                                stroke="url(#paint1_linear_6446_1492)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M25 30C25 31.3261 25.5268 32.5979 26.4645 33.5355C27.4021 34.4732 28.6739 35 30 35C31.3261 35 32.5979 34.4732 33.5355 33.5355C34.4732 32.5979 35 31.3261 35 30C35 28.6739 34.4732 27.4021 33.5355 26.4645C32.5979 25.5268 31.3261 25 30 25C28.6739 25 27.4021 25.5268 26.4645 26.4645C25.5268 27.4021 25 28.6739 25 30Z"
                                stroke="url(#paint2_linear_6446_1492)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M33.6667 33.6667L36.6667 36.6667"
                                stroke="url(#paint3_linear_6446_1492)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </g>
                        <defs>
                            <linearGradient
                                id="paint0_linear_6446_1492"
                                x1="5"
                                y1="11.1081"
                                x2="30.1205"
                                y2="33.2608"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint1_linear_6446_1492"
                                x1="15.001"
                                y1="17.034"
                                x2="23.3692"
                                y2="24.4148"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint2_linear_6446_1492"
                                x1="25"
                                y1="27.04"
                                x2="33.39"
                                y2="34.44"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint3_linear_6446_1492"
                                x1="33.6667"
                                y1="34.2787"
                                x2="36.1837"
                                y2="36.4987"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <clipPath id="clip0_6446_1492">
                                <rect
                                    width="40"
                                    height="40"
                                    fill="white"
                                />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard.admin.config.smtp.index') }}">
            <div
                class="flex items-center gap-1 rounded-xl p-5 shadow-md shadow-black/5 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-black/[7%] dark:bg-heading-foreground/[2%]">
                <div>
                    <h3 class="mb-0 text-2xs text-current">{{__("Email")}}</h3>
                    <p class="m-0 text-2xs text-current opacity-80">{{__("SMTP Settings")}}</p>
                </div>
                <div class="ms-auto">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="40"
                        height="40"
                        viewBox="0 0 40 40"
                        fill="none"
                    >
                        <g clip-path="url(#clip0_6446_1502)">
                            <path
                                d="M13.3333 20C13.3333 21.7681 14.0356 23.4638 15.2859 24.714C16.5361 25.9643 18.2318 26.6666 19.9999 26.6666C21.768 26.6666 23.4637 25.9643 24.714 24.714C25.9642 23.4638 26.6666 21.7681 26.6666 20C26.6666 18.2319 25.9642 16.5362 24.714 15.2859C23.4637 14.0357 21.768 13.3333 19.9999 13.3333C18.2318 13.3333 16.5361 14.0357 15.2859 15.2859C14.0356 16.5362 13.3333 18.2319 13.3333 20Z"
                                stroke="url(#paint0_linear_6446_1502)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M26.6667 20V22.5C26.6667 23.6051 27.1056 24.6649 27.8871 25.4463C28.6685 26.2277 29.7283 26.6667 30.8333 26.6667C31.9384 26.6667 32.9982 26.2277 33.7796 25.4463C34.561 24.6649 35 23.6051 35 22.5V20C35.0041 16.7768 33.97 13.6381 32.0507 11.0486C30.1314 8.45923 27.429 6.55691 24.344 5.62349C21.2589 4.69007 17.9553 4.77519 14.9224 5.86626C11.8895 6.95733 9.28874 8.99629 7.50534 11.6811C5.72194 14.3659 4.85079 17.5538 5.02092 20.7725C5.19105 23.9911 6.39342 27.0694 8.44992 29.5513C10.5064 32.0331 13.3077 33.7866 16.4387 34.5518C19.5697 35.3171 22.8639 35.0534 25.8333 33.8"
                                stroke="url(#paint1_linear_6446_1502)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </g>
                        <defs>
                            <linearGradient
                                id="paint0_linear_6446_1502"
                                x1="13.3333"
                                y1="16.0533"
                                x2="24.5199"
                                y2="25.92"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint1_linear_6446_1502"
                                x1="5"
                                y1="11.1007"
                                x2="30.17"
                                y2="33.3007"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <clipPath id="clip0_6446_1502">
                                <rect
                                    width="40"
                                    height="40"
                                    fill="white"
                                />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard.admin.config.gdpr.index') }}">
            <div
                class="flex items-center gap-1 rounded-xl p-5 shadow-md shadow-black/5 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-black/[7%] dark:bg-heading-foreground/[2%]">
                <div>
                    <h3 class="mb-0 text-2xs text-current">{{__("GDPR")}}</h3>
                    <p class="m-0 text-2xs text-current opacity-80">{{__("Manage Cookies")}}</p>
                </div>
                <div class="ms-auto">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="40"
                        height="40"
                        viewBox="0 0 40 40"
                        fill="none"
                    >
                        <g clip-path="url(#clip0_6446_1546)">
                            <path
                                d="M13.3333 21.6667V21.685"
                                stroke="url(#paint0_linear_6446_1546)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M20 28.3333V28.3516"
                                stroke="url(#paint1_linear_6446_1546)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M20 20V20.0183"
                                stroke="url(#paint2_linear_6446_1546)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M26.6667 23.3333V23.3516"
                                stroke="url(#paint3_linear_6446_1546)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M18.3333 13.3333V13.3516"
                                stroke="url(#paint4_linear_6446_1546)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M21.9134 5.79335L26.3584 7.63335C25.4775 8.79238 25.0001 10.2079 24.9993 11.6637C24.9985 13.1196 25.4742 14.5357 26.3539 15.6957C27.2335 16.8557 28.4687 17.6959 29.8708 18.0879C31.2728 18.4799 32.7647 18.4022 34.1184 17.8667L34.2067 18.0867C34.7142 19.3118 34.7142 20.6883 34.2067 21.9133C33.3784 23.2233 32.8101 24.2533 32.5001 25C32.1851 25.76 31.8184 26.99 31.4001 28.6933C30.8922 29.9182 29.9186 30.8913 28.6934 31.3983C26.9467 31.8367 25.7151 32.2033 25.0001 32.5C24.2084 32.8283 23.1801 33.3967 21.9134 34.2067C20.6883 34.7141 19.3118 34.7141 18.0867 34.2067C16.7501 33.3683 15.7217 32.8 15.0001 32.5C14.2151 32.175 12.9834 31.8083 11.3067 31.4C10.0818 30.8922 9.10883 29.9186 8.60174 28.6933C8.16008 26.94 7.79341 25.71 7.50008 25C7.17008 24.2017 6.60008 23.1733 5.79341 21.9133C5.28599 20.6883 5.28599 19.3118 5.79341 18.0867C6.59341 16.8467 7.16008 15.8183 7.50008 15C7.78508 14.3117 8.15174 13.08 8.60008 11.3067C9.10792 10.0818 10.0815 9.10877 11.3067 8.60168C13.0267 8.17501 14.2567 7.80835 15.0001 7.50001C15.7634 7.18335 16.7917 6.61501 18.0867 5.79335C19.3118 5.28592 20.6883 5.28592 21.9134 5.79335Z"
                                stroke="url(#paint5_linear_6446_1546)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </g>
                        <defs>
                            <linearGradient
                                id="paint0_linear_6446_1546"
                                x1="13.3333"
                                y1="21.6704"
                                x2="13.3339"
                                y2="21.7014"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint1_linear_6446_1546"
                                x1="20"
                                y1="28.3371"
                                x2="20.0006"
                                y2="28.368"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint2_linear_6446_1546"
                                x1="20"
                                y1="20.0037"
                                x2="20.0006"
                                y2="20.0347"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint3_linear_6446_1546"
                                x1="26.6667"
                                y1="23.3371"
                                x2="26.6674"
                                y2="23.368"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint4_linear_6446_1546"
                                x1="18.3333"
                                y1="13.3371"
                                x2="18.3339"
                                y2="13.368"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint5_linear_6446_1546"
                                x1="5.41284"
                                y1="11.3644"
                                x2="29.8902"
                                y2="32.9535"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <clipPath id="clip0_6446_1546">
                                <rect
                                    width="40"
                                    height="40"
                                    fill="white"
                                />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard.admin.config.login.index') }}">
            <div
                class="flex items-center gap-1 rounded-xl p-5 shadow-md shadow-black/5 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-black/[7%] dark:bg-heading-foreground/[2%]">
                <div>
                    <h3 class="mb-0 text-2xs text-current">{{__("Login")}}</h3>
                    <p class="m-0 text-2xs text-current opacity-80">{{__("Social Login Preferences")}}</p>
                </div>
                <div class="ms-auto">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="44"
                        height="44"
                        viewBox="0 0 44 44"
                        fill="none"
                    >
                        <g clip-path="url(#clip0_6440_1039)">
                            <path
                                d="M5.5 22C5.5 24.1668 5.92678 26.3124 6.75599 28.3143C7.58519 30.3161 8.80057 32.1351 10.3327 33.6673C11.8649 35.1994 13.6839 36.4148 15.6857 37.244C17.6876 38.0732 19.8332 38.5 22 38.5C24.1668 38.5 26.3124 38.0732 28.3143 37.244C30.3161 36.4148 32.1351 35.1994 33.6673 33.6673C35.1994 32.1351 36.4148 30.3161 37.244 28.3143C38.0732 26.3124 38.5 24.1668 38.5 22C38.5 19.8332 38.0732 17.6876 37.244 15.6857C36.4148 13.6839 35.1994 11.8649 33.6673 10.3327C32.1351 8.80057 30.3161 7.58519 28.3143 6.75599C26.3124 5.92679 24.1668 5.5 22 5.5C19.8332 5.5 17.6876 5.92679 15.6857 6.75599C13.6839 7.58519 11.8649 8.80057 10.3327 10.3327C8.80057 11.8649 7.58519 13.6839 6.75599 15.6857C5.92678 17.6876 5.5 19.8332 5.5 22Z"
                                stroke="url(#paint0_linear_6440_1039)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M16.5 18.3333C16.5 19.792 17.0795 21.191 18.1109 22.2224C19.1424 23.2539 20.5413 23.8333 22 23.8333C23.4587 23.8333 24.8576 23.2539 25.8891 22.2224C26.9205 21.191 27.5 19.792 27.5 18.3333C27.5 16.8746 26.9205 15.4757 25.8891 14.4442C24.8576 13.4128 23.4587 12.8333 22 12.8333C20.5413 12.8333 19.1424 13.4128 18.1109 14.4442C17.0795 15.4757 16.5 16.8746 16.5 18.3333Z"
                                stroke="url(#paint1_linear_6440_1039)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M11.3081 34.5565C11.7619 33.0462 12.6904 31.7225 13.9559 30.7816C15.2214 29.8407 16.7565 29.3328 18.3334 29.3333H25.6668C27.2457 29.3328 28.7827 29.8419 30.0491 30.7849C31.3156 31.728 32.2438 33.0546 32.6958 34.5675"
                                stroke="url(#paint2_linear_6440_1039)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </g>
                        <defs>
                            <linearGradient
                                id="paint0_linear_6440_1039"
                                x1="5.5"
                                y1="12.232"
                                x2="33.187"
                                y2="36.652"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint1_linear_6440_1039"
                                x1="16.5"
                                y1="15.0773"
                                x2="25.729"
                                y2="23.2173"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint2_linear_6440_1039"
                                x1="11.3081"
                                y1="30.4011"
                                x2="13.5887"
                                y2="38.6205"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <clipPath id="clip0_6440_1039">
                                <rect
                                    width="44"
                                    height="44"
                                    fill="white"
                                />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard.admin.config.finance.index') }}">
            <div
                class="flex items-center gap-1 rounded-xl p-5 shadow-md shadow-black/5 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-black/[7%] dark:bg-heading-foreground/[2%]">
                <div>
                    <h3 class="mb-0 text-2xs text-current">{{__("Finance")}}</h3>
                    <p class="m-0 text-2xs text-current opacity-80">{{__("Billing & Affiliate")}}</p>
                </div>
                <div class="ms-auto">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="40"
                        height="40"
                        viewBox="0 0 40 40"
                        fill="none"
                    >
                        <g clip-path="url(#clip0_6446_1542)">
                            <path
                                d="M28.3334 13.3334V8.33335C28.3334 7.89133 28.1578 7.4674 27.8453 7.15484C27.5327 6.84228 27.1088 6.66669 26.6667 6.66669H10.0001C9.11603 6.66669 8.26818 7.01788 7.64306 7.643C7.01794 8.26812 6.66675 9.11597 6.66675 10M6.66675 10C6.66675 10.8841 7.01794 11.7319 7.64306 12.357C8.26818 12.9822 9.11603 13.3334 10.0001 13.3334H30.0001C30.4421 13.3334 30.866 13.5089 31.1786 13.8215C31.4912 14.1341 31.6667 14.558 31.6667 15V20M6.66675 10V30C6.66675 30.8841 7.01794 31.7319 7.64306 32.357C8.26818 32.9822 9.11603 33.3334 10.0001 33.3334H30.0001C30.4421 33.3334 30.866 33.1578 31.1786 32.8452C31.4912 32.5326 31.6667 32.1087 31.6667 31.6667V26.6667"
                                stroke="url(#paint0_linear_6446_1542)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M33.3333 20V26.6667H26.6666C25.7825 26.6667 24.9347 26.3155 24.3096 25.6904C23.6844 25.0652 23.3333 24.2174 23.3333 23.3333C23.3333 22.4493 23.6844 21.6014 24.3096 20.9763C24.9347 20.3512 25.7825 20 26.6666 20H33.3333Z"
                                stroke="url(#paint1_linear_6446_1542)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </g>
                        <defs>
                            <linearGradient
                                id="paint0_linear_6446_1542"
                                x1="6.66675"
                                y1="12.1067"
                                x2="28.8153"
                                y2="30.4208"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint1_linear_6446_1542"
                                x1="23.3333"
                                y1="21.36"
                                x2="28.7569"
                                y2="28.5355"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <clipPath id="clip0_6446_1542">
                                <rect
                                    width="40"
                                    height="40"
                                    fill="white"
                                />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard.admin.config.more.index') }}">
            <div
                class="flex items-center gap-1 rounded-xl p-5 shadow-md shadow-black/5 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-black/[7%] dark:bg-heading-foreground/[2%]">
                <div>
                    <h3 class="mb-0 text-2xs text-current">{{__("More")}}</h3>
                    <p class="m-0 text-2xs text-current opacity-80">{{__("Advanced Options")}}</p>
                </div>
                <div class="ms-auto">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="40"
                        height="40"
                        viewBox="0 0 40 40"
                        fill="none"
                    >
                        <g clip-path="url(#clip0_6446_1516)">
                            <path
                                d="M5 20C5 21.9698 5.38799 23.9204 6.14181 25.7403C6.89563 27.5601 8.00052 29.2137 9.3934 30.6066C10.7863 31.9995 12.4399 33.1044 14.2597 33.8582C16.0796 34.612 18.0302 35 20 35C21.9698 35 23.9204 34.612 25.7403 33.8582C27.5601 33.1044 29.2137 31.9995 30.6066 30.6066C31.9995 29.2137 33.1044 27.5601 33.8582 25.7403C34.612 23.9204 35 21.9698 35 20C35 18.0302 34.612 16.0796 33.8582 14.2597C33.1044 12.4399 31.9995 10.7863 30.6066 9.3934C29.2137 8.00052 27.5601 6.89563 25.7403 6.14181C23.9204 5.38799 21.9698 5 20 5C18.0302 5 16.0796 5.38799 14.2597 6.14181C12.4399 6.89563 10.7863 8.00052 9.3934 9.3934C8.00052 10.7863 6.89563 12.4399 6.14181 14.2597C5.38799 16.0796 5 18.0302 5 20Z"
                                stroke="url(#paint0_linear_6446_1516)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M13.3333 20V20.0183"
                                stroke="url(#paint1_linear_6446_1516)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M20 20V20.0183"
                                stroke="url(#paint2_linear_6446_1516)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M26.6667 20V20.0183"
                                stroke="url(#paint3_linear_6446_1516)"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </g>
                        <defs>
                            <linearGradient
                                id="paint0_linear_6446_1516"
                                x1="5"
                                y1="11.12"
                                x2="30.17"
                                y2="33.32"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint1_linear_6446_1516"
                                x1="13.3333"
                                y1="20.0037"
                                x2="13.3339"
                                y2="20.0347"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint2_linear_6446_1516"
                                x1="20"
                                y1="20.0037"
                                x2="20.0006"
                                y2="20.0347"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <linearGradient
                                id="paint3_linear_6446_1516"
                                x1="26.6667"
                                y1="20.0037"
                                x2="26.6674"
                                y2="20.0347"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                            <clipPath id="clip0_6446_1516">
                                <rect
                                    width="40"
                                    height="40"
                                    fill="white"
                                />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>
        </a>

    @endsection

    @push('script')
    @endpush
