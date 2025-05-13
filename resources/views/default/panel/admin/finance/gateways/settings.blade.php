@extends('panel.layout.settings')
@section('title', __($options['title']) . ' ' . __('Settings'))
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-5"
        id="settings_form"
        action="{{ route('dashboard.admin.finance.paymentGateways.settings.save') }}"
        enctype="multipart/form-data"
        method="post"
    >
        @csrf

        <input
            id="code"
            type="hidden"
            name="code"
            value="{{ $options['code'] }}"
        />
        <div class="flex flex-wrap items-center justify-between gap-4">
            <x-forms.input
                id="is_active"
                type="checkbox"
                name="is_active"
                label="{{ __('Enable Gateway') }}"
                :checked="$settings['is_active'] == true"
                switcher
            />
            @php
                $country_tax_enabled = true;

                if ($options['code'] == 'cryptomus') {
                    $country_tax_enabled = $settings['country_tax_enabled'];
                }
            @endphp



            <div class="flex ">


                @if ($options['tax'] == 1 && $country_tax_enabled)
                    <x-modal
                            title="{{ __('Tax Setting') }}"
                            disable-modal="{{ $app_is_demo }}"
                            disable-modal-message="{{ __('This feature is disabled in Demo version.') }}"
                    >
                        <x-slot:trigger>
                              {{ __('Tax Setting') }}
                        </x-slot:trigger>
                        <x-slot:modal>
                            <form
                                    action="{{ route('dashboard.admin.finance.paymentGateways.settings.tax.save') }}"
                                    method="POST"
                            >
                                @csrf

                                <input
                                        type="hidden"
                                        name="code"
                                        value="{{ $options['code'] }}"
                                />
                                @if($options['code'] == 'cryptomus')
                                    <div>
                                        <label class="mb-2">@lang('Country')</label>
                                        <select class="form-control mb-3 mt-2" name="country_code">
                                            @foreach(\App\Services\CountryCodeService::countryCodes($without = []) as $code => $country)
                                                <option value="{{ $code }}"> {{ $country. " ($code)" }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <x-forms.input
                                        id="tax"
                                        name="tax"
                                        value="{{ $settings->tax }}"
                                        size="lg"
                                        label="{{ __('Tax Rate (%)') }}"
                                        required
                                />

                                <x-alert class="mt-3">
                                    <p>
                                        {{ __('Editing the tax will have no impact on existing users or lead to cancellations.') }}
                                    </p>
                                </x-alert>

                                <div class="mt-4 border-t pt-3">
                                    <x-button
                                            @click.prevent="modalOpen = false"
                                            variant="outline"
                                            type="button"
                                    >
                                        {{ __('Cancel') }}
                                    </x-button>
                                    <x-button type="submit">
                                        {{ __('Save changes') }}
                                    </x-button>
                                </div>
                            </form>
                        </x-slot:modal>
                    </x-modal>

                @endif

                    @if($options['code'] == 'cryptomus')
                        <x-forms.input
                                class="ms-2"
                                id="country_tax_enabled"
                                type="checkbox"
                                name="country_tax_enabled"
                                label="{{ __('Country tax') }}"
                                :checked="$settings['country_tax_enabled'] == 1"
                                switcher
                        />
                    @endif
            </div>


        </div>





        @if ($options['mode'] == 1)
            <x-forms.input
                id="mode"
                type="select"
                name="mode"
                label="{{ __('Gateway Mode') }}"
                size="lg"
            >
                <option
                    value="live"
                    @selected($settings->mode == 'live')
                >
                    Live
                </option>
                <option
                    value="sandbox"
                    @selected($settings->mode == 'sandbox')
                >
                    Sandbox
                </option>
            </x-forms.input>
            @if ($settings->mode == null)
                <p class="-mt-3 text-red-500 dark:text-red-300">
                    {{ __('Please save setting with the mode you want.') }}
                </p>
            @endif
        @endif

        @if ($options['currency'] == 1)
            <x-forms.input
                id="currency"
                type="select"
                name="currency"
                label="{{ __('Default Currency') }}"
                size="lg"
                required
            >
                {!! $currencies !!}
            </x-forms.input>
        @endif

        @if ($options['currency_locale'] == 1)
            <x-forms.input
                id="currency_locale"
                name="currency_locale"
                placeholder="en_US"
                value="{{ $settings->currency_locale }}"
                required
            >
                <x-slot:label>
                    {{ __('Currency Locale') }} ({{ __('in format') }}: en_US)
                    <a
                        class="underline"
                        href="https://developer.paypal.com/api/rest/reference/locale-codes/"
                        target="_blank"
                    >
                        ({{ __('PayPal') }})
                    </a>
                </x-slot:label>
            </x-forms.input>
        @endif

        @if ($app_is_demo)
            @if ($options['live_client_id'] == 1)
                <x-forms.input
                    id="sandbox_client_id"
                    type="text"
                    name="sandbox_client_id"
                    value="*************"
                    size="lg"
                    label="{{ __('Api Key / Client Id') }}"
                />
            @endif
            @if ($options['live_client_secret'] == 1)
                <x-forms.input
                    id="sandbox_client_secret"
                    name="sandbox_client_secret"
                    value="*****************"
                    size="lg"
                    label="{{ __('Api Secret / Secret Key') }}"
                />
            @endif
        @else
            @if ($options['live_client_id'] == 1)
                <x-forms.input
                    id="live_client_id"
                    name="live_client_id"
                    value="{{ $app_is_demo ? '*****************' : $settings->live_client_id }}"
                    size="lg"
                    label="{{ __('Api Key / Client Id') }}"
                    required
                />
            @endif
            @if ($options['live_client_secret'] == 1)
                <x-forms.input
                    id="live_client_secret"
                    name="live_client_secret"
                    value="{{ $app_is_demo ? '*****************' : $settings->live_client_secret }}"
                    size="lg"
                    label="{{ __('Api Secret / Secret Key') }}"
                    required
                />
            @endif
            @if ($options['live_app_id'] == 1)
                <x-forms.input
                    id="live_app_id"
                    name="live_app_id"
                    value="{{ $settings->live_app_id }}"
                    required
                    size="lg"
                    label="{{ __('App ID / App Name / Merchant') }}"
                />
            @endif
            @if ($options['base_url'] == 1)
                <x-forms.input
                    id="base_url"
                    name="base_url"
                    value="{{ $settings->base_url ?? ($options['code'] == 'stripe' ? 'https://api.stripe.com' : '') }}"
                    required
                    size="lg"
                    label="{{ __('Base URL') }}"
                />
            @endif
        @endif

        @if ($options['sandbox_client_id'] == 1)
            <x-forms.input
                id="sandbox_client_id"
                name="sandbox_client_id"
                value="{{ $settings->sandbox_client_id }}"
                size="lg"
                label="{{ __('Api Key / Client Id') . ' ' }} ({{ __('Sandbox') }})"
            />
        @endif
        @if ($options['sandbox_client_secret'] == 1)
            <x-forms.input
                id="sandbox_client_secret"
                name="sandbox_client_secret"
                value="{{ $settings->sandbox_client_secret }}"
                size="lg"
                label="{{ __('Api Secret / Secret Key') . ' ' }} ({{ __('Sandbox') }})"
            />
        @endif
        @if ($options['sandbox_app_id'] == 1)
            <x-forms.input
                id="sandbox_app_id"
                name="sandbox_app_id"
                value="{{ $settings->sandbox_app_id }}"
                size="lg"
                label="{{ __('App ID / App Name / Merchant') . ' ' }} ({{ __('Sandbox') }})"
            />
        @endif
        @if ($options['sandbox_url'] == 1)
            <x-forms.input
                id="sandbox_url"
                name="sandbox_url"
                value="{{ $settings->sandbox_url }}"
                size="lg"
                label="{{ __('Base URL') . ' ' }} ({{ __('Sandbox') }})"
            />
        @endif

        <!-- bankTransfer fields start -->
        @if ($options['bank_account_other'] == 1)
            <x-forms.input
                id="bank_account_other"
                name="bank_account_other"
                size="lg"
                label="{{ __('Payment Intructions') }}"
                type="textarea"
            >{{ $settings->bank_account_other ?? 'To facilitate the processing of your transaction, kindly remit your payment directly to our designated bank account. Please ensure to include your Order ID Number as the payment reference to expedite the allocation of funds to your account. Note that services will not be credited until the payment has successfully been received in our bank account. We appreciate your cooperation and thank you for choosing our services.' }}</x-forms.input>
        @endif
        @if ($options['bank_account_details'] == 1)
            <x-forms.input
                id="bank_account_details"
                name="bank_account_details"
                size="lg"
                label="{{ __('Bank Account Details') }}"
                type="textarea"
                rows="7"
            >{{ $settings->bank_account_details ?? "Bank Name:\nAccount Name:\nIBAN:\nBIC/Swift:\nRouting Number:\n" }}</x-forms.input>
        @endif

		@if($options['code'] === 'stripe')
			<div class="border rounded-lg p-3">
				@if(isset($options['automate_tax']) && $options['automate_tax'])
					<x-forms.input
						id="automate_tax"
						type="checkbox"
						name="automate_tax"
						label="{{ __('Automate taxes') }}"
						:checked="$settings['automate_tax'] == 1"
						switcher
					/>
				@endif

				<x-alert class="mt-3">
					<p>
						{{ __('Automate taxes will automatically calculate taxes based on the user\'s country.') }}
					</p>
				</x-alert>
			</div>
		@endif

		@if($options['code'] === 'stripe' || $options['code'] === 'razorpay')
			<x-forms.input
				disabled
				value="{{ url('webhook').'/' . $options['code'] }}"
				id="bank_account_details"
				name="bank_account_details"
				size="lg"
				label="{{ __('Webhook') }}"
				rows="7"
			/>
			<!-- bankTransfer fields end -->
		@endif


        @if ($app_is_demo)
            <x-button
                variant="primary"
                size="lg"
                onclick="return toastr.info('This feature is disabled in Demo version.')"
            >
                {{ __('Save') }}
            </x-button>
        @else
            <x-button
                id="settings_button"
                size="lg"
                type="submit"
            >
                {{ __('Save') }}
            </x-button>
        @endif

        <input
            id="title"
            type="hidden"
            name="title"
            value="{{ $options['title'] }}"
        />
    </form>

    @if($options['code'] == 'cryptomus' && $taxes->count() && $settings['country_tax_enabled'])
        <div class="mt-4 lqd-table-outline rounded-xl border border-table-border pt-1 lqd-table-wrap w-full max-w-full overflow-x-auto">
            <table class="lqd-table w-full text-start overflow-x-auto [-webkit-overflow-scrolling:touch] max-w-full">
                <thead class="lqd-table-head border-b text-start text-4xs leading-tight uppercase tracking-wider font-medium text-label transition-border">
                    <tr>
                        <th>country code</th>
                        <th>tax</th>
                        <th>action</th>
                    </tr>
                </thead>
                @foreach($taxes as $tax)
                    <tbody class="[&_tr:not(:last-child)]:border-b">
                        <tr class="group active">
                            <td>{{ $tax->country_code }}</td>
                            <td>{{ $tax->tax }}</td>
                            <td>
                                <x-button
                                        class="size-9"
                                        hover-variant="danger"
                                        size="none"
                                        variant="ghost-shadow"
                                        href="{{ route('dashboard.admin.finance.paymentGateways.settings.tax.delete', $tax->id) }}"
                                        onclick="return confirm('{{ __('Are you sure?') }}')"
                                        title="{{ __('Delete') }}"
                                >
                                    <x-tabler-x class="size-4" />
                                </x-button>
                            </td>
                        </tr>
                    </tbody>
                @endforeach
            </table>
        </div>
    @endif

    @if ($options['code'] != 'banktransfer')
        <x-alert class="mt-5">
            <p class="mb-4">
                {{ __('What happens when you save?') }}
            </p>

            <ul class="mb-4 list-inside list-disc">
                <li>{{ __('Save your settings.') }}</li>
                <li>{{ __('Check all membership plans for this gateway.') }}</li>
                <li>{{ __('Remove all products and prices defined before for old settings.') }}</li>
                <li>{{ __('Cancel all old subscriptions. Acquired amounts do not reset.') }}</li>
                <li>{{ __('Generate new product definitions in your new gateway account.') }}</li>
                <li>{{ __('Generate new price definitions in your new gateway account.') }}</li>
                <li>{{ __('Remove all webhooks defined before and create new webhook.') }}</li>
            </ul>

            <p>
                {{ __('Note that we do not store old keys. So every save action is new.') }}
            </p>

            <p>
                {{ __('This process will take time. So, please be patient and wait until success message appears.') }}
            </p>
        </x-alert>
    @endif
@endsection
@push('script')
    <script>
        $('#country_tax_enabled').on('change', function () {
            $.ajax({
                type: 'POST',
                url: '{{ route('dashboard.admin.finance.paymentGateways.country.tax.enabled', $options['code']) }}',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(resultData) {
                    toastr.success(resultData.message);
                    setTimeout(() => {
                        location.reload()
                    }, 1500);
                }
            });
        })
    </script>
@endpush
