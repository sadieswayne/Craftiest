{!! adsense_pricing_728x90() !!}

<section
    class="site-section relative py-10 transition-all duration-700 md:translate-y-8 md:opacity-0 [&.lqd-is-in-view]:translate-y-0 [&.lqd-is-in-view]:opacity-100"
    id="pricing"
>
    <div class="container relative">
        <div class="relative rounded-[50px] border p-11 max-lg:px-5">
            <x-section-header
                mb="7"
                title="{!! __($fSectSettings->pricing_title) !!}"
                subtitle="{!! __($fSectSettings->pricing_description) ?? __('Flexible and affording plans tailored to your needs. Save up to %20 for a limited time.') !!}"
            />
            <div class="lqd-tabs text-center">
                <div class="lqd-tabs-triggers mx-auto mb-9 inline-flex flex-wrap gap-2 rounded-md border text-[15px] font-medium leading-none">
                    @php
                        $plan = $plansSubscriptionMonthly;
                    @endphp

                    @if ($plansSubscriptionMonthly->count() > 0)
                        @include('landing-page.pricing.item-trigger', [
                            'target' => '#pricing-monthly',
                            'label' => __('Monthly'),
                            'active' => true,
                            'currency' => $currency,
                        ])
                    @endif
                    @if ($plansSubscriptionAnnual->count() > 0)
                        @include('landing-page.pricing.item-trigger', [
                            'target' => '#pricing-annual',
                            'label' => __('Annual'),
                            'badge' => __($fSectSettings->pricing_save_percent),
                            'currency' => $currency,
                        ])
                    @endif
                    @if ($plansSubscriptionLifetime->count() > 0)
                        @include('landing-page.pricing.item-trigger', [
                            'target' => '#pricing-lifetime',
                            'label' => __('Lifetime'),
                            'currency' => $currency,
                        ])
                    @endif
                    @if ($plansPrepaid->count() > 0)
                        @include('landing-page.pricing.item-trigger', [
                            'target' => '#pricing-prepaid',
                            'label' => __('Pre-Paid'),
                            'currency' => $currency,
                        ])
                    @endif
                </div>
                <div class="lqd-tabs-content-wrap px-10 max-xl:px-0">
                    <div class="lqd-tabs-content">
                        <div id="pricing-monthly">
                            <div class="grid grid-cols-3 gap-2 max-md:grid-cols-1">
                                @foreach ($plansSubscriptionMonthly as $plan)
                                    @include('landing-page.pricing.item-content', ['period' => $plan->frequency === 'monthly' ? 'Per Month' : 'Per Year'])
                                @endforeach
                            </div>
                        </div>
                        <div
                            class="hidden"
                            id="pricing-annual"
                        >
                            <div class="grid grid-cols-3 gap-2 max-md:grid-cols-1">
                                @foreach ($plansSubscriptionAnnual as $plan)
                                    @include('landing-page.pricing.item-content', ['period' => $plan->frequency === 'monthly' ? 'Per Month' : 'Per Year'])
                                @endforeach
                            </div>
                        </div>
                        <div
                            class="hidden"
                            id="pricing-prepaid"
                        >
                            <div class="grid grid-cols-3 gap-2 max-md:grid-cols-1">
                                @foreach ($plansPrepaid as $plan)
                                    @include('landing-page.pricing.item-content', ['period' => __('One Time Payment')])
                                @endforeach
                            </div>
                        </div>
                        <div
                            class="hidden"
                            id="pricing-lifetime"
                        >
                            <div class="grid grid-cols-3 gap-2 max-md:grid-cols-1">
                                @foreach ($plansSubscriptionLifetime as $plan)
                                    @include('landing-page.pricing.item-content', ['period' => __('One Time Payment')])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-9 flex justify-center">
                <div class="flex w-[305px] items-center gap-5 text-[12px] text-[#002A40] text-opacity-60">
                    <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#6C727B] bg-opacity-10">
                        <svg
                            width="13"
                            height="18"
                            viewBox="0 0 13 18"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M10.346 6.323H4.024V3.449C4.024 2.839 4.26632 2.25399 4.69765 1.82266C5.12899 1.39132 5.714 1.149 6.324 1.149C6.934 1.149 7.51901 1.39132 7.95035 1.82266C8.38168 2.25399 8.624 2.839 8.624 3.449C8.624 3.6015 8.68458 3.74775 8.79241 3.85559C8.90025 3.96342 9.0465 4.024 9.199 4.024C9.3515 4.024 9.49775 3.96342 9.60558 3.85559C9.71342 3.74775 9.774 3.6015 9.774 3.449C9.774 2.534 9.41052 1.65648 8.76352 1.00948C8.11652 0.362482 7.23899 -0.000999451 6.324 -0.000999451C5.409 -0.000999451 4.53148 0.362482 3.88448 1.00948C3.23748 1.65648 2.874 2.534 2.874 3.449V6.323H2.3C1.69001 6.323 1.10499 6.56532 0.673653 6.99666C0.242319 7.42799 0 8.013 0 8.623V14.946C0 15.248 0.0594935 15.5471 0.175079 15.8262C0.290665 16.1052 0.460078 16.3588 0.673653 16.5723C0.887227 16.7859 1.14078 16.9553 1.41983 17.0709C1.69888 17.1865 1.99796 17.246 2.3 17.246H10.347C10.649 17.246 10.9481 17.1865 11.2272 17.0709C11.5062 16.9553 11.7598 16.7859 11.9733 16.5723C12.1869 16.3588 12.3563 16.1052 12.4719 15.8262C12.5875 15.5471 12.647 15.248 12.647 14.946V8.622C12.6469 8.31996 12.5872 8.0209 12.4715 7.7419C12.3558 7.46291 12.1863 7.20943 11.9726 6.99595C11.759 6.78247 11.5053 6.61316 11.2262 6.49769C10.9472 6.38223 10.648 6.32287 10.346 6.323Z"
                                fill="#6C727B"
                            />
                        </svg>
                    </span>
                    <p class="[&_strong]:block">{!! __('<strong>Safe Payment:</strong> Use Stripe or Credit Card.') !!}</p>
                </div>
            </div>
        </div>
    </div>
</section>
