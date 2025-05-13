{{-- @if (count($chat->messages) > 1) --}}
@if ($chat != null)
    @foreach ($chat->messages as $message)
        @if ($message->input != null)
            <div class="lqd-chat-user-bubble mb-2 flex flex-row-reverse content-end gap-[8px] lg:ms-auto">
                <span class="text-dark">
                    @php
                        $avatarUrl = isset(Auth::user()->avatar) ? Auth::user()->avatar : url('/assets/img/auth/default-avatar.png');
                        if (str_starts_with(Auth::user()->avatar, 'upload') || str_starts_with(Auth::user()->avatar, 'assets')) {
                            $avatarUrl = '/' . Auth::user()->avatar;
                        }
                    @endphp
                    <span
                        class="avatar h-[24px] w-[24px] shrink-0"
                        style="background-image: url('{{ $avatarUrl }}')"
                    ></span>
                </span>
                <div
                    class="chat-content-container group relative mb-[7px] max-w-[calc(100%-64px)] rounded-[2em] border-none bg-[#F3E2FD] text-[#090A0A] dark:bg-[rgba(var(--tblr-primary-rgb),0.3)] dark:text-white">
                    <div class="chat-content px-[1.5rem] py-[0.75rem]">
                        {{ $message->input }}
                    </div>
                    <div
                        class="lqd-clipboard-copy-wrap group/copy-wrap pointer-events-auto invisible absolute -start-5 bottom-0 opacity-0 transition-all group-hover:!visible group-hover:!opacity-100">
                        <button
                            class="lqd-clipboard-copy inline-flex h-10 w-10 items-center justify-center rounded-full border-none bg-white p-0 text-black !shadow-lg transition-all hover:-translate-y-[2px] hover:scale-110"
                            data-copy-options='{ "content": ".chat-content", "contentIn": "<.chat-content-container" }'
                            title="{{ __('Copy to clipboard') }}"
                        >
                            <span class="sr-only">{{ __('Copy to clipboard') }}</span>
                            <x-tabler-copy class="size-4" />
                        </button>
                    </div>
                </div>
            </div>
            @if ($message->pdfPath != null && $message->pdfPath != '')
                <div class="mb-2 mr-[30px] flex flex-row-reverse content-end gap-[8px] lg:ms-auto">
                    <svg
                        width="36"
                        height="36"
                        viewBox="0 0 36 36"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M23.7762 0H5.11921C4.59978 0 4.17871 0.421071 4.17871 1.23814V35.3571C4.17871 35.5789 4.59978 36 5.11921 36H30.8811C31.4005 36 31.8216 35.5789 31.8216 35.3571V8.343C31.8216 7.89557 31.7618 7.75157 31.6564 7.6455L24.1761 0.165214C24.07 0.0597857 23.926 0 23.7762 0Z"
                            fill="#E9E9E0"
                        />
                        <path
                            d="M24.1074 0.0970459V7.71426H31.7246L24.1074 0.0970459Z"
                            fill="#D9D7CA"
                        />
                        <path
                            d="M12.5445 21.4226C12.3208 21.4226 12.1061 21.35 11.9229 21.2131C11.2537 20.711 11.1637 20.1523 11.2061 19.7718C11.3231 18.7252 12.6172 17.6298 15.0536 16.5138C16.0205 14.3949 16.9404 11.7843 17.4888 9.60306C16.8472 8.20677 16.2236 6.3952 16.6781 5.33256C16.8375 4.96035 17.0362 4.67492 17.4071 4.55149C17.5537 4.50263 17.924 4.44092 18.0603 4.44092C18.3843 4.44092 18.669 4.85813 18.8709 5.11527C19.0605 5.35699 19.4906 5.86935 18.6311 9.48799C19.4977 11.2777 20.7255 13.1008 21.902 14.3493C22.7448 14.1969 23.4699 14.1191 24.0607 14.1191C25.0674 14.1191 25.6775 14.3538 25.9263 14.8372C26.132 15.2371 26.0478 15.7044 25.6755 16.2258C25.3175 16.7266 24.8238 16.9914 24.2484 16.9914C23.4667 16.9914 22.5564 16.4977 21.5413 15.5225C19.7175 15.9037 17.5878 16.5838 15.8662 17.3366C15.3288 18.4771 14.8138 19.3957 14.3343 20.0694C13.6753 20.9919 13.107 21.4226 12.5445 21.4226ZM14.2558 18.1273C12.882 18.8994 12.3221 19.5339 12.2816 19.8913C12.2752 19.9505 12.2578 20.1061 12.5587 20.3362C12.6545 20.306 13.2138 20.0508 14.2558 18.1273ZM23.0225 15.2718C23.5464 15.6748 23.6743 15.8786 24.017 15.8786C24.1674 15.8786 24.5962 15.8722 24.7948 15.5951C24.8906 15.4608 24.9279 15.3746 24.9427 15.3283C24.8636 15.2866 24.7588 15.2017 24.1873 15.2017C23.8627 15.2023 23.4545 15.2165 23.0225 15.2718ZM18.2203 11.0405C17.7607 12.6309 17.1538 14.348 16.5013 15.9031C17.8449 15.3817 19.3055 14.9266 20.6773 14.6045C19.8095 13.5965 18.9423 12.3378 18.2203 11.0405ZM17.8301 5.60063C17.7671 5.62185 16.9751 6.73013 17.8918 7.66806C18.5019 6.30842 17.8578 5.59163 17.8301 5.60063Z"
                            fill="#CC4B4C"
                        />
                        <path
                            d="M30.8811 36H5.11921C4.59978 36 4.17871 35.5789 4.17871 35.0595V25.0714H31.8216V35.0595C31.8216 35.5789 31.4005 36 30.8811 36Z"
                            fill="#CC4B4C"
                        />
                        <path
                            d="M11.176 34.0714H10.1211V27.594H11.9841C12.2592 27.594 12.5318 27.6377 12.8012 27.7258C13.0705 27.8139 13.3122 27.9456 13.5263 28.1211C13.7404 28.2966 13.9133 28.5094 14.0451 28.7582C14.1769 29.007 14.2431 29.2866 14.2431 29.5978C14.2431 29.9263 14.1872 30.2233 14.076 30.4901C13.9647 30.7569 13.8092 30.9812 13.6099 31.1625C13.4106 31.3438 13.1702 31.4846 12.8892 31.5842C12.6083 31.6839 12.2972 31.7334 11.9577 31.7334H11.1754L11.176 34.0714ZM11.176 28.3937V30.96H12.1429C12.2715 30.96 12.3987 30.9381 12.5254 30.8938C12.6514 30.8501 12.7671 30.7781 12.8725 30.6784C12.978 30.5788 13.0628 30.4399 13.1271 30.2612C13.1914 30.0825 13.2235 29.8614 13.2235 29.5978C13.2235 29.4924 13.2087 29.3702 13.1798 29.2333C13.1502 29.0957 13.0905 28.9639 12.9998 28.8379C12.9085 28.7119 12.7812 28.6065 12.6173 28.5216C12.4534 28.4368 12.2361 28.3944 11.9667 28.3944L11.176 28.3937Z"
                            fill="white"
                        />
                        <path
                            d="M20.7121 30.6527C20.7121 31.1856 20.6549 31.6414 20.5404 32.0194C20.426 32.3974 20.2814 32.7137 20.1052 32.9689C19.9291 33.2241 19.7317 33.4247 19.5119 33.5713C19.292 33.7179 19.0799 33.8271 18.8748 33.9011C18.6697 33.9744 18.482 34.0213 18.3123 34.0419C18.1426 34.0611 18.0166 34.0714 17.9343 34.0714H15.4824V27.594H17.4335C17.9786 27.594 18.4576 27.6808 18.8703 27.8531C19.283 28.0254 19.6263 28.2561 19.8989 28.5429C20.1714 28.8296 20.3746 29.1568 20.5096 29.5226C20.6446 29.889 20.7121 30.2657 20.7121 30.6527ZM17.5833 33.2981C18.2981 33.2981 18.8137 33.0699 19.13 32.6128C19.4463 32.1557 19.6044 31.4936 19.6044 30.6264C19.6044 30.357 19.5723 30.0902 19.508 29.8266C19.4431 29.5631 19.319 29.3246 19.1345 29.1105C18.95 28.8964 18.6993 28.7235 18.383 28.5917C18.0667 28.4599 17.6566 28.3937 17.1526 28.3937H16.5374V33.2981H17.5833Z"
                            fill="white"
                        />
                        <path
                            d="M23.3135 28.3937V30.4329H26.0206V31.1535H23.3135V34.0714H22.2412V27.594H26.2925V28.3937H23.3135Z"
                            fill="white"
                        />
                    </svg>
                </div>
                <div class="mb-2 mr-[30px] flex flex-row-reverse content-end gap-[8px] lg:ms-auto">
                    <a
                        class="flex"
                        href="{{ $message->pdfPath }}"
                    >
                        <label>{{ $message->pdfName }}</label>
                        <svg
                            width="17"
                            height="18"
                            viewBox="0 0 17 18"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <mask
                                id="mask0_3243_893"
                                style="mask-type:alpha"
                                maskUnits="userSpaceOnUse"
                                x="0"
                                y="0"
                                width="17"
                                height="18"
                            >
                                <rect
                                    y="0.43103"
                                    width="17"
                                    height="17"
                                    fill="#D9D9D9"
                                />
                            </mask>
                            <g mask="url(#mask0_3243_893)">
                                <path
                                    d="M4.45937 12.9289L3.71973 12.1892L10.69 5.21212H4.35314V4.14966H12.4989V12.2955H11.4365V5.95858L4.45937 12.9289Z"
                                    fill="#1C1B1F"
                                />
                            </g>
                        </svg>
                    </a>
                </div>
            @endif
            @if ($message->images != null)
                @foreach (explode(',', $message->images) as $image)
                    <div class="lqd-chat-image-bubble mb-2 flex flex-row-reverse content-end gap-[8px] lg:ms-auto">
                        <div class="mb-[7px] flex w-[80%] justify-end rounded-[2em] border-none text-[#090A0A] dark:text-white md:w-[50%]">
                            <img
                                class="img-content rounded-[2em] px-[1.5rem] py-[0.75rem]"
                                src={{ $image }}
                            />
                        </div>
                    </div>
                @endforeach
            @endif
        @endif
        <div class="lqd-chat-ai-bubble mb-2 flex content-start gap-[8px]">
            <span class="text-dark">
                <span
                    class="avatar h-[24px] w-[24px] shrink-0"
                    style="background-image: url('{{ !empty($chat->category->image) ? '/' . $chat->category->image : asset('assets/img/auth/default-avatar.png') }}')"
                ></span>
            </span>
            @if ($message->output != null)
                <div
                    class="chat-content-container group relative mb-[7px] max-w-[calc(100%-64px)] rounded-[2em] border-none bg-[#E5E7EB] text-[#090A0A] dark:bg-[rgba(255,255,255,0.02)] dark:text-white">
                    <pre class="chat-content m-0 w-full whitespace-pre-wrap bg-transparent px-[1.5rem] py-[0.75rem] indent-0 font-[inherit] text-[1em] font-normal text-inherit">{!! $message->output !!}</pre>
                    <div
                        class="lqd-clipboard-copy-wrap group/copy-wrap pointer-events-auto invisible absolute -end-5 bottom-0 opacity-0 transition-all group-hover:!visible group-hover:!opacity-100">
                        <button
                            class="lqd-clipboard-copy inline-flex h-10 w-10 items-center justify-center rounded-full border-none bg-white p-0 text-black !shadow-lg transition-all hover:-translate-y-[2px] hover:scale-110"
                            data-copy-options='{ "content": ".chat-content", "contentIn": "<.chat-content-container" }'
                            title="{{ __('Copy to clipboard') }}"
                        >
                            <span class="sr-only">{{ __('Copy to clipboard') }}</span>
                            <x-tabler-copy class="size-4" />
                        </button>
                    </div>
                </div>
            @endif
        </div>
        @if ($message->outputImage != null && $message->outputImage != '')
            <div class="lqd-chat-image-bubble mb-2 flex gap-[8px] lg:ms-auto">
                <div class="mb-[7px] flex w-[80%] justify-end rounded-[2em] border-none text-[#090A0A] dark:text-white md:w-[50%]">
                    <img
                        class="img-content rounded-[2em] px-[1.5rem] py-[0.75rem]"
                        src={{ $message->outputImage }}
                    />
                </div>
            </div>
        @endif
    @endforeach
    @if (count($chat->messages) == 0)
        <div class="mb-2 flex content-end">
            <div class="w-full-none rounded-[2em] border bg-[#F3E2FD] text-[#090A0A] dark:bg-[rgba(255,255,255,0.02)] dark:text-white">
                <div class="chat-content px-[1.5rem] py-[0.75rem]">
                    {{ __('You have no message... Please start typing.') }}
                </div>
            </div>
        </div>
    @endif
@endif
