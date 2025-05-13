@php
    $features = ['SEO Tool / Checker', 'AI Social Media', 'WordPress Integration', 'Cryptomus Payment', 'Cloudflare R2 Storage', '+ All Upcoming Extensions'];
@endphp

<section
    class="border-b border-white/15 py-32 max-md:py-24"
    id="premium-support-access"
>
    <div class="container">
        <div class="flex flex-wrap items-center justify-between gap-y-8">
            <div class="w-full lg:w-1/2 lg:text-center">
                <svg
                    class="inline-block"
                    width="136"
                    height="213"
                    viewBox="0 0 136 213"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M73.0774 211V67.075L29.7484 94.951L2.47839 53.743L82.7734 1.62695H133.98V211H73.0774Z"
                        stroke="url(#paint0_linear_260_2)"
                        stroke-width="3"
                    />
                    <defs>
                        <linearGradient
                            id="paint0_linear_260_2"
                            x1="115.209"
                            y1="148.5"
                            x2="58.5297"
                            y2="170.278"
                            gradientUnits="userSpaceOnUse"
                        >
                            <stop
                                offset="0.0001"
                                stop-color="#9DE8EE"
                            />
                            <stop
                                offset="0.490049"
                                stop-color="#995BFF"
                            />
                            <stop
                                offset="1"
                                stop-color="#8CEDD6"
                            />
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            <div class="w-full lg:w-1/2">
                <div class="mx-auto lg:w-8/12">
                    <h6 class="mb-6 inline-block rounded-full border border-white/15 bg-white/5 px-4 py-2 font-mono font-semibold leading-tight tracking-wide">
                        @lang('Full Access to Marketplace')
                    </h6>
                    <h2 class="mb-5 text-[52px] leading-[0.94em]">
                        @lang('Free Access to All Extensions.')
                    </h2>
                    <p class="mb-8 text-[19px] leading-[1.42em]">
                        @lang('Get access to our')
                        <span class="text-white">
                            @lang('expansive library of extensions at no extra cost.')
                        </span>
                        @lang("With your exclusive membership, you'll have the privilege of instantly downloading new marketplace items, regardless of their price.")
                    </p>

                    <div class="flex flex-wrap">
                        <div>
                            <ol class="space-y-3.5 text-xs font-medium text-white">
                                @foreach ($features as $feature)
                                    <li class="flex items-center gap-4">
                                        <svg
                                            width="15"
                                            height="16"
                                            viewBox="0 0 15 16"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M1.54948 7.37072C1.25608 7.37154 0.968912 7.45542 0.721194 7.61264C0.473476 7.76986 0.275333 7.994 0.149689 8.25914C0.0240441 8.52427 -0.0239674 8.81956 0.0112089 9.11084C0.0463851 9.40212 0.163311 9.67749 0.34846 9.9051L4.2954 14.7401C4.43613 14.9148 4.61652 15.0535 4.82159 15.1445C5.02666 15.2355 5.25049 15.2763 5.47448 15.2635C5.95356 15.2377 6.38608 14.9815 6.66184 14.5601L14.8606 1.35593C14.862 1.35373 14.8634 1.35154 14.8648 1.34939C14.9418 1.23127 14.9168 0.997192 14.758 0.850142C14.7144 0.809761 14.663 0.778736 14.6069 0.75898C14.5508 0.739223 14.4913 0.731153 14.432 0.735266C14.3727 0.739379 14.3149 0.755589 14.2621 0.782896C14.2093 0.810204 14.1627 0.848031 14.125 0.894048C14.1221 0.897666 14.1191 0.90123 14.1159 0.904739L5.84734 10.247C5.81588 10.2826 5.77766 10.3115 5.73492 10.3322C5.69218 10.3528 5.64575 10.3648 5.59835 10.3674C5.55094 10.3699 5.5035 10.363 5.45878 10.3471C5.41406 10.3312 5.37294 10.3065 5.33783 10.2746L2.59364 7.77735C2.30863 7.51608 1.93612 7.37102 1.54948 7.37072Z"
                                                fill="url(#paint0_linear_144_159)"
                                            />
                                            <defs>
                                                <linearGradient
                                                    id="paint0_linear_144_159"
                                                    x1="9.23602e-08"
                                                    y1="3.69866"
                                                    x2="12.2269"
                                                    y2="14.7613"
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
                                            </defs>
                                        </svg>
                                        @lang($feature)
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
