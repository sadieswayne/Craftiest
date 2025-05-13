@props([
    'site_name' => isset($site_name) ? $site_name : 'MagicAi'
])

<x-card
        class="text-center shadow-xl shadow-black/5"
        variant="shadow"
>
    <svg
            class="mx-auto"
            width="265"
            height="265"
            viewBox="0 0 265 265"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
    >
        <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M111.3 132.5L50.3496 193.45V214.65H71.5496L76.8496 209.35V201.4H84.7996L98.0496 188.15V180.2H106L132.5 153.7L134.023 155.224C136.077 157.277 139.456 157.277 141.51 155.224L144.822 151.911L139.522 146.611L120.972 128.061L113.022 120.111L109.71 123.424C107.656 125.477 107.656 128.856 109.71 130.91L111.233 132.434L111.3 132.5Z"
                stroke="url(#paint0_linear_393_362)"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
        />
        <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M159 50.35C156.018 50.35 153.368 51.5425 151.447 53.53L106.53 98.4475C104.608 100.369 103.35 103.019 103.35 106C103.35 108.981 104.542 111.631 106.53 113.552L151.447 158.47C153.368 160.391 156.018 161.65 159 161.65C161.981 161.65 164.631 160.457 166.552 158.47L211.47 113.552C213.457 111.631 214.65 108.915 214.65 106C214.65 103.085 213.457 100.369 211.47 98.4475L166.552 53.53C164.631 51.6087 161.981 50.35 159 50.35Z"
                stroke="url(#paint1_linear_393_362)"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
        />
        <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M172.251 100.7C176.623 100.7 180.201 97.1225 180.201 92.75C180.201 88.3775 176.623 84.8 172.251 84.8C167.878 84.8 164.301 88.3775 164.301 92.75C164.301 97.1225 167.878 100.7 172.251 100.7Z"
                stroke="url(#paint2_linear_393_362)"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
        />
        <path
                d="M111.299 148.4L60.9492 198.75"
                stroke="url(#paint3_linear_393_362)"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
        />
        <path
                d="M132.5 50.35C87.1184 50.35 50.3496 87.1187 50.3496 132.5C50.3496 145.882 53.5296 158.47 59.2271 169.6M95.3996 205.772C106.53 211.404 119.183 214.65 132.5 214.65C177.881 214.65 214.65 177.881 214.65 132.5C214.65 130.181 214.583 127.929 214.385 125.676M139.323 50.6812C137.071 50.4825 134.818 50.4162 132.5 50.4162"
                stroke="#EDF3F8"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
        />
        <path
                d="M126.073 63.9313C91.0271 67.1775 63.5996 96.6588 63.5996 132.5C63.5996 142.173 65.5871 151.315 69.1646 159.663M105.337 195.835C113.685 199.413 122.827 201.4 132.5 201.4C168.407 201.4 197.822 173.973 201.068 138.926"
                stroke="#EDF3F8"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
        />
        <defs>
            <linearGradient
                    id="paint0_linear_393_362"
                    x1="97.5859"
                    y1="120.111"
                    x2="97.5859"
                    y2="214.65"
                    gradientUnits="userSpaceOnUse"
            >
                <stop stop-color="#69C79D" />
                <stop
                        offset="1"
                        stop-color="#7976E8"
                />
            </linearGradient>
            <linearGradient
                    id="paint1_linear_393_362"
                    x1="159"
                    y1="50.35"
                    x2="159"
                    y2="161.65"
                    gradientUnits="userSpaceOnUse"
            >
                <stop stop-color="#69C79D" />
                <stop
                        offset="1"
                        stop-color="#7976E8"
                />
            </linearGradient>
            <linearGradient
                    id="paint2_linear_393_362"
                    x1="172.251"
                    y1="84.8"
                    x2="172.251"
                    y2="100.7"
                    gradientUnits="userSpaceOnUse"
            >
                <stop stop-color="#69C79D" />
                <stop
                        offset="1"
                        stop-color="#7976E8"
                />
            </linearGradient>
            <linearGradient
                    id="paint3_linear_393_362"
                    x1="86.1242"
                    y1="148.4"
                    x2="86.1242"
                    y2="198.75"
                    gradientUnits="userSpaceOnUse"
            >
                <stop stop-color="#69C79D" />
                <stop
                        offset="1"
                        stop-color="#7976E8"
                />
            </linearGradient>
        </defs>
    </svg>
    <form
            class="mb-6"
            action="{{ route('LaravelInstaller::license') }}"
            method="POST"
    >
        @csrf

        <h2 class="mb-9 text-[36px] font-bold tracking-[-0.015em]">{{ __('Activate your license') }} </h2>
        <div class="lqd-input-container relative mb-3 hidden" >
            <label class="lqd-input-label flex cursor-pointer items-center gap-2 text-2xs font-medium leading-none text-label mb-3" for="title">
                License Key
            </label>
            <input
                    readonly
                    id="license_key"
                    class="lqd-input block peer w-full px-4 py-2 border border-input-border bg-input-background text-input-foreground text-base ring-offset-0 transition-colors focus:border-secondary focus:outline-0 focus:ring focus:ring-secondary dark:focus:ring-foreground/10 sm:text-2xs lqd-input-lg h-11 rounded-xl"
                    name="liquid_license_domain_key"
                    value="{{ old('liquid_license_domain_key', data_get($portal, 'liquid_license_domain_key')) }}"
                    type="text"
                    placeholder="Enter Title Here"
            >
        </div>
        <x-button
                class="w-full {{ isset($button) ? $button : '' }}"
                size="lg"
                type="submit"
        >
            {{ __('Activate') }}
        </x-button>
    </form>
</x-card>
