@inject('gatewayControls', 'App\Http\Controllers\Finance\GatewayController')
@php
    $activeGateways = \App\Models\Gateways::where('is_active', 1)->get();
    $type = strpos(Route::currentRouteName(), 'startPrepaidPaymentProcess') !== false ? 'startPrepaidPaymentProcess' : 'startSubscriptionProcess';
@endphp
@if ($activeGateways->count() > 1)
    <div class="my-2 w-full">
        <x-card
            class="border"
            size="lg"
        >
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($activeGateways as $gateway)
                    @php($data = $gatewayControls->gatewayData($gateway->code))
                    <x-card
                        class="hover:-translate-y-1 hover:border-transparent hover:shadow-xl hover:shadow-black/5"
                        class:body="flex items-center gap-4"
                        variant="outline"
                        onclick="window.location.href='{{ route('dashboard.user.payment.' . $type, ['planId' => $plan->id, 'gatewayCode' => $gateway->code]) }}';"
                        style="cursor:pointer;"
                    >
                        <div class="size-11 inline-grid place-content-center rounded-xl bg-card-background shadow-[0_2px_10px_#7C8DB51F]">
                            @if ($data['whiteLogo'] == 1)
                                <img
                                    class="rounded-3xl bg-primary px-3"
                                    src="{{ custom_theme_url($data['img']) }}"
                                    style="max-height:24px;"
                                    alt="{{ $data['title'] }}"
                                />
                            @else
                                <img
                                    class="rounded-3xl px-3"
                                    src="{{ custom_theme_url($data['img']) }}"
                                    style="max-height:24px;"
                                    alt="{{ $data['title'] }}"
                                />
                            @endif
                        </div>
                        <div>
                            <h5 class="mb-1 text-lg font-medium">
                                {{ $data['title'] }}
                            </h5>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </x-card>
    </div>
@endif
