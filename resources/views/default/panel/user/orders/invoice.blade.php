@extends('panel.layout.app')
@section('title', __('Order #' . \Illuminate\Support\Str::upper($invoice->order_id)))
@section('titlebar_actions')
    <button
        class="btn btn-primary"
        type="button"
        onclick="javascript:window.print();"
    >
        <svg
            class="!me-2"
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
            <path
                stroke="none"
                d="M0 0h24v24H0z"
                fill="none"
            />
            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
            <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
        </svg>
        {{ __('Print Invoice') }}
    </button>
@endsection
@section('additional_css')
    <style>
        .printable-logo {
            visibility: hidden;
        }
    </style>
    <style media="print">
        body {
            visibility: hidden;
        }

        .printable-section {
            visibility: visible;
            margin-top: -15%;
        }

        .printable-logo {
            visibility: visible;
        }
    </style>
@endsection
@section('content')
    <!-- Page body -->
    <div class="py-10">
        <div class="container-xl">
            <div class="printable-section">
                <div class="row">
                    <div class="col-12 printable-logo mb-6">
                        <img
                            class="transition-all group-[.lqd-is-sticky]/header:peer-first:translate-x-2 group-[.lqd-is-sticky]/header:peer-first:opacity-0"
                            src="{{ custom_theme_url($setting->logo_path, true) }}"
                            @if (isset($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                            alt="{{ $setting->site_name }} logo"
                        >
                    </div>
                    <div class="col-12 mb-4">
                        <p class="strong m-0">{{ __('Order Ref') }}
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                            <span>{{ \Illuminate\Support\Str::upper($invoice->order_id) }}</span>
                        </p>
                        <p class="strong m-0">{{ __('Order Date/Time') }}&nbsp;:
                            <span>{{ date('j.n.Y', strtotime($invoice->created_at)) }} -
                                {{ date('H:i:s', strtotime($invoice->created_at)) }}</span>
                        </p>
                    </div>
                    <div class="col-6">
                        <p class="h3">{{ __('Client') }}</p>
                        <address>
                            {{ $invoice->user?->fullName() }}<br>
							{{ $invoice->user->email }}<br>
							{{ $invoice->user->phone }}<br>
							{{ $invoice->user->address }}<br>
							@if($invoice->user->postal || $invoice->user->state || $invoice->user->city || $invoice->user->country)
								{{ $invoice->user->postal ? $invoice->user->postal . ', ' : '' }}
								{{ $invoice->user->state ? $invoice->user->state . ', ' : '' }}
								{{ $invoice->user->city ? $invoice->user->city . ', ' : '' }}
								{{ $invoice->user->country ?? '' }}
							@else
								{{__('Not Available')}}
							@endif
                        </address>
                    </div>
                    <div class="col-6 !text-end">
                        <p class="h3">{{ $setting->invoice_name }}</p>
                        <address>
                            {{ $setting->invoice_address }}<br>
                            {{ $setting->invoice_state }}{{ isset($setting->invoice_city) ? ',' : '' }}
                            {{ $setting->invoice_city }}<br>
                            {{ $setting->invoice_country }}{{ isset($setting->invoice_city) ? ',' : '' }}
                            {{ $setting->invoice_postal }}<br>
                            {{ $setting->invoice_website }}<br>
                            {{ $setting->invoice_phone }}
                        </address>
                    </div>
                </div>
                <table class="table-transparent table-responsive table">
                    <thead>
                        <tr>
                            <th
                                class="text-center"
                                style="width: 1%"
                            ></th>
                            <th>{{ __('Product') }}</th>
                            <th
                                class="text-center"
                                style="width: 1%"
                            >{{ __('Qnt') }}</th>
                            <th
                                class="!text-end"
                                style="width: 1%"
                            >{{ __('Unit') }}</th>
                            <th
                                class="!text-end"
                                style="width: 1%"
                            >{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tr>
                        <td class="text-center">1</td>
                        <td>
                            <p class="strong mb-1">{{ @$invoice->plan->name ?? __('Archived Plan') }}</p>
                            <div class="text-muted">
                                @if ($invoice->type == 'subscription')
                                    {{ __('Subscription Plan Payment') }}
                                @else
                                    {{ __('Prepaid Plan Payment') }}
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            1
                        </td>
                        <td class="!text-end">
                            @if (currencyShouldDisplayOnRight(currency()->symbol))
                                {{ $invoice->price }}{{ currency()->symbol }}
                            @else
                                {{ currency()->symbol }}{{ $invoice->price }}
                            @endif

                        </td>
                        <td class="!text-end">
                            @if (currencyShouldDisplayOnRight(currency()->symbol))
                                {{ $invoice->price }}{{ currency()->symbol }}
                            @else
                                {{ currency()->symbol }}{{ $invoice->price }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td
                            class="strong !text-end"
                            colspan="4"
                        >{{ __('Subtotal') }}</td>
                        <td class="!text-end">
                            @if (currencyShouldDisplayOnRight(currency()->symbol))
                                {{ $invoice->price - $invoice->tax_value }}{{ currency()->symbol }}
                            @else
                                {{ currency()->symbol }}{{ $invoice->price - $invoice->tax_value }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td
                            class="strong !text-end"
                            colspan="4"
                        >{{ __('Vat Rate') }}</td>
                        <td class="!text-end">{{ $invoice->tax_rate }}%</td>
                    </tr>
                    <tr>
                        <td
                            class="strong !text-end"
                            colspan="4"
                        >{{ __('Vat Due') }}</td>
                        @if (isset($invoice->tax_rate) and $invoice->tax_rate > 0)
                            <td class="!text-end">
                                @if (currencyShouldDisplayOnRight(currency()->symbol))
                                    {{ $invoice->tax_value }}{{ currency()->symbol }}
                                @else
                                    {{ currency()->symbol }}{{ $invoice->tax_value }}
                                @endif
                            </td>
                        @else
                            <td class="!text-end">-</td>
                        @endif
                    </tr>
                    <tr>
                        <td
                            class="strong text-uppercase !text-end"
                            colspan="4"
                        >{{ __('Total Due') }}</td>
                        <td class="font-weight-bold !text-end">
                            @if (currencyShouldDisplayOnRight(currency()->symbol))
                                {{ $invoice->price }}{{ currency()->symbol }}
                            @else
                                {{ currency()->symbol }}{{ $invoice->price }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <p class="text-muted mt-5 text-center">
                {{ __('Thank you very much for doing business with us. We look forward to working with you again!') }}</p>
        </div>
    </div>
@endsection
