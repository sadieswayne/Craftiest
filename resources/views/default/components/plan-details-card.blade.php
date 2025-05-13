@php
    use App\Enums\Plan\TypeEnum;
    $isPrepaid = $plan->type === TypeEnum::TOKEN_PACK->value;

    $check_html =
        '<svg width="13" height="10" viewBox="0 0 13 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M3.952 7.537L11.489 0L12.452 1L3.952 9.5L1.78814e-07 5.545L1 4.545L3.952 7.537Z" /></svg>';

    if ($style === 'style-2') {
        $check_html =
            '<svg width="18" height="20" viewBox="0 0 18 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <path d="M17.5728 7.36998C16.7313 3.75745 13.8485 1.04729 10.044 0.902054C8.86481 0.872321 7.69279 1.09405 6.60594 1.55248C5.51909 2.01091 4.54233 2.69553 3.74063 3.5608C1.14076 6.25529 -0.663742 10.1508 0.515633 13.9072C1.47511 16.9635 4.51407 19.0664 7.66126 19.2585C7.95168 19.2755 8.24284 19.2759 8.53332 19.2598C12.2406 19.0648 15.9386 16.795 17.2561 13.2322C17.927 11.35 18.0371 9.31351 17.5728 7.36998ZM15.6263 10.3694C15.4727 12.4244 14.4871 14.747 12.6276 15.8725C10.2526 17.31 7.48081 17.9771 4.79592 16.2672C0.144195 13.3047 2.89201 6.21862 6.76197 3.95114C11.6718 1.27717 15.9697 5.22059 15.6263 10.3694ZM13.3476 6.53204C13.1017 6.37491 12.8157 6.29207 12.5239 6.29348C12.2322 6.29489 11.947 6.38049 11.7026 6.53999C10.9822 6.97942 10.5096 7.72064 9.95659 8.33624C9.13013 9.25623 8.44066 10.0087 7.6274 10.9452C7.28363 11.341 7.26278 11.2369 7.12351 11.0202C6.74033 10.4239 6.32828 9.59144 5.77887 9.1399C5.60053 8.99742 5.38015 8.91777 5.15193 8.91331C4.9237 8.90886 4.70038 8.97985 4.51662 9.11527C4.33286 9.25069 4.19892 9.44297 4.13559 9.66228C4.07226 9.88159 4.08308 10.1157 4.16637 10.3282C4.61654 11.3102 5.12533 12.2641 5.68999 13.185C6.08794 13.8904 6.70385 14.7441 7.51812 14.6225C8.25061 14.5131 9.06983 13.3413 9.56498 12.6538C10.2051 11.7648 10.9013 10.9728 11.565 10.1382C12.0488 9.52983 12.5956 8.86574 13.1288 8.30118C13.3977 8.04741 13.5954 7.72756 13.7021 7.37354C13.7355 7.21388 13.7195 7.04781 13.6562 6.89747C13.5929 6.74713 13.4852 6.61965 13.3476 6.53204Z"/> </svg>';
    }
@endphp

<ul class="mt-6 w-full px-1 text-left max-lg:p-0">
    <li class="relative mb-3 last:mb-0">
        <span @class([
            'inline-grid align-middle me-3',
            'size-22px shrink-0 place-content-center rounded-xl bg-[#684AE2] bg-opacity-10 text-[#684AE2]' =>
                $style === 'style-1',
        ])>
            {!! $check_html !!}
        </span>

        {{ __('Access') }} <strong>{{ $isPrepaid ? __('All') : __($plan->checkOpenAiItemCount()) }}</strong> {{ __('Features') }}

        <div class="group inline-block sm:relative sm:before:absolute sm:before:-inset-2.5">
            <span class="peer relative -mt-6 inline-flex !h-6 !w-6 cursor-pointer items-center justify-center">
                <x-tabler-info-circle-filled class="size-4 opacity-20" />
            </span>
            <div
                class="lqd-price-table-info pointer-events-none invisible absolute start-full top-1/2 z-10 ms-2 max-h-96 min-w-60 -translate-y-1/2 translate-x-2 scale-105 overflow-y-auto rounded-lg border bg-background p-5 opacity-0 shadow-xl transition-all before:absolute before:-start-2 before:top-0 before:h-full before:w-2 group-hover:pointer-events-auto group-hover:visible group-hover:translate-x-0 group-hover:opacity-100 max-sm:!end-0 max-sm:!start-0 max-sm:!top-full max-sm:!me-0 max-sm:!ms-0 max-sm:mt-4 max-sm:!translate-x-0 max-sm:!translate-y-0 [&.anchor-end]:end-2 [&.anchor-end]:start-auto [&.anchor-end]:me-2 [&.anchor-end]:ms-0"
                data-set-anchor="true"
            >
                <ul>
                    @foreach ($allFeatures as $key => $openAi)
                        <li class="mb-3 mt-5 first:mt-0">
                            <h5 class="text-base">{{ ucfirst($key) }}</h5>
                        </li>
                        @php
                            $openAi = \App\Helpers\Classes\Helper::sortingOpenAiSelected($openAi, $plan->open_ai_items);
                        @endphp
                        @foreach ($openAi as $itemOpenAi)
                            @php
                                $exist = $plan->checkOpenAiItem($itemOpenAi->slug);
                                if ($isPrepaid && $plan->checkOpenAiItemCount() <= 0) {
                                    $exist = true;
                                }
                            @endphp
                            <li class="mb-1.5 flex items-center gap-1.5 text-heading-foreground">
                                <span @class([
                                    'bg-[#684AE2] bg-opacity-10 text-[#684AE2]' => $exist,
                                    'bg-foreground/10 text-foreground' => !$exist,
                                    'size-4 inline-flex items-center justify-center rounded-xl align-middle',
                                ])>
                                    @if ($exist)
                                        <x-tabler-check class="size-3" />
                                    @else
                                        <x-tabler-minus class="size-3" />
                                    @endif
                                </span>
                                <small @class(['opacity-60' => !$exist])>
                                    {{ $itemOpenAi->title }}
                                </small>
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        </div>
    </li>
    <li class="relative mb-3 last:mb-0">
        <span @class([
            'inline-grid align-middle me-3',
            'size-22px shrink-0 place-content-center rounded-xl bg-[#684AE2] bg-opacity-10 text-[#684AE2]' =>
                $style === 'style-1',
        ])>
            {!! $check_html !!}
        </span>

        @lang('Plan Credits')
        <div class="group inline-block sm:relative sm:before:absolute sm:before:-inset-2.5">
            <span class="peer relative -mt-6 inline-flex !h-6 !w-6 cursor-pointer items-center justify-center">
                <x-tabler-info-circle-filled class="size-4 opacity-20" />
            </span>
            <div
                class="lqd-price-table-info pointer-events-none invisible absolute start-full top-1/2 z-10 ms-2 max-h-96 min-w-60 -translate-y-1/2 translate-x-2 scale-105 overflow-y-auto rounded-lg border bg-background p-5 opacity-0 shadow-xl transition-all before:absolute before:-start-2 before:top-0 before:h-full before:w-2 group-hover:pointer-events-auto group-hover:visible group-hover:translate-x-0 group-hover:opacity-100 max-sm:!end-0 max-sm:!start-0 max-sm:!top-full max-sm:!me-0 max-sm:!ms-0 max-sm:mt-4 max-sm:!translate-x-0 max-sm:!translate-y-0 [&.anchor-end]:end-2 [&.anchor-end]:start-auto [&.anchor-end]:me-2 [&.anchor-end]:ms-0"
                data-set-anchor="true"
            >
                <x-credit-list
                    :plan="$plan"
                    showType="directly"
                    tooltipClass="max-w-48"
                />
            </div>
        </div>
    </li>
    @if ($plan->is_team_plan)
        <li class="mb-3 last:mb-0">
            <span @class([
                'inline-grid align-middle me-3',
                'size-22px shrink-0 place-content-center rounded-xl bg-[#684AE2] bg-opacity-10 text-[#684AE2]' =>
                    $style === 'style-1',
            ])>
                {!! $check_html !!}
            </span>
            <strong>
                {{ number_format($plan->plan_allow_seat) }}
            </strong>
            {{ __('Team allow seats') }}
        </li>
    @endif
    @if ($plan->trial_days > 0)
        <li class="mb-3 flex items-center last:mb-0">
            <span @class([
                'inline-grid align-middle me-3',
                'size-22px shrink-0 place-content-center rounded-xl bg-[#684AE2] bg-opacity-10 text-[#684AE2]' =>
                    $style === 'style-1',
            ])>
                {!! $check_html !!}
            </span>
            {{ number_format($plan->trial_days) . ' ' . __('Days of free trial.') }}
        </li>
    @endif
    @if (!empty($plan->features))
        @foreach (explode(',', $plan->features) as $feature)
            <li class="mb-3 flex items-center last:mb-0">
                <span @class([
                    'inline-grid align-middle me-3',
                    'size-22px shrink-0 place-content-center rounded-xl bg-[#684AE2] bg-opacity-10 text-[#684AE2]' =>
                        $style === 'style-1',
                ])>
                    {!! $check_html !!}
                </span>
                {{ trim(__($feature)) }}
            </li>
        @endforeach
    @endif
</ul>
