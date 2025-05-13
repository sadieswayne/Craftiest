<div @class([
    'fixed z-50 hidden invisible opacity-0 transition-opacity group-[.lqd-is-sticky]/header:opacity-100 group-[.lqd-is-sticky]/header:visible md:block',
    'start-8 bottom-8' => $app_is_not_demo,
    'start-24 bottom-6' => $app_is_demo,
])>
    <a
        class="flex items-center gap-5 rounded-xl bg-white px-3 py-3 text-[12px] text-[#002A40] text-opacity-60 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:scale-110 hover:shadow-md"
        data-fslightbox="html5-youtube-videos"
        href="{{ !empty($fSetting->floating_button_link) ? $fSetting->floating_button_link : '#' }}"
    >
        <span class="lqd-is-in-view inline-flex shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#3655df] via-[#A068FA] via-70% to-[#327BD1]">
            <svg
                style="padding: 16px;"
                xmlns="http://www.w3.org/2000/svg"
                width="45"
                height="45"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor"
                fill="none"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path
                    stroke="none"
                    d="M0 0h24v24H0z"
                    fill="none"
                ></path>
                <path
                    d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z"
                    stroke-width="0"
                    fill="#fff"
                ></path>
            </svg>
        </span>
        <span class="[&_strong]:block">{!! __($fSetting->floating_button_small_text) !!} <strong class="text-[0.9rem] text-black">{!! __($fSetting->floating_button_bold_text) !!} &nbsp;</strong></span>
    </a>
</div>
