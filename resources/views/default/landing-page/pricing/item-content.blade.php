<div class="px-7 pt-7 pb-11 rounded-3xl text-center {{  $plan->is_featured ? 'border' : '' }} max-xl:px-6 max-lg:px-4">
    <h6 class="mb-6 rounded-md border p-[0.35rem] text-[11px] text-opacity-80">{{ __($plan->name) }}</h6>
    <p class="mb-1 text-[45px] font-medium leading-none -tracking-wide text-heading-foreground">

        @if (currencyShouldDisplayOnRight(currency()->symbol))
            {{ formatPrice($plan->price, 2) }}<sup class="text-[0.53em]">{{ currency()->symbol }}</sup>
        @else
            <sup class="text-[0.53em]">{{ currency()->symbol }}</sup>{{ formatPrice($plan->price, 2) }}
        @endif

    </p>
    <p class="mb-4 text-sm opacity-60">{{ __($period) }}</p>
    <a
        class="block w-full rounded-lg bg-black bg-opacity-[0.03] p-3 font-medium text-heading-foreground transition-colors hover:bg-black hover:text-white"
        href="{{ route('register', ['plan' => $plan->id]) }}"
    >{{ __('Select').'  '.__($plan->name) }}</a>

    <x-plan-details-card
        :plan="$plan"
        :period="$period"
    />
</div>
