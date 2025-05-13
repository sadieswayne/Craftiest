@extends('panel.layout.settings',['layout' => 'fullwidth'])
@section('title', __('Finance Settings'))
@section('titlebar_actions', '')

@section('settings')
    <form action="{{ route('dashboard.admin.config.finance.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-5 mx-auto">
				<x-form-step
					class="mb-4 mt-5"
					step="1"
					label="{{ __('Affiliate Settings') }}"
				>
				</x-form-step>
				<div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Affiliate Minimum Withdrawal') }}</label>
                            <input
                                    class="form-control"
                                    id="affiliate_minimum_withdrawal"
                                    type="number"
                                    name="affiliate_minimum_withdrawal"
                                    value="{{ $setting->affiliate_minimum_withdrawal }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Affiliate Commission Percentage') }} (%)</label>
                            <input
                                    class="form-control"
                                    id="affiliate_commission_percentage"
                                    type="number"
                                    name="affiliate_commission_percentage"
                                    value="{{ $setting->affiliate_commission_percentage }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Default Currency') }}</label>
                            <select
                                    class="form-select"
                                    id="default_currency"
                                    name="default_currency"
                            >
                                @include('panel.admin.settings.currencies')
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Default Country') }}</label>
                            <select
                                    class="form-select"
                                    id="default_country"
                                    name="default_country"
                            >
                                @include('panel.admin.settings.countries')
                            </select>
                        </div>
                    </div>

					<div class="col-md-12">
							<div class="mb-3">
								<x-card class="mb-3">
									<h4 class="mb-3">{{ __('Affiliate Program') }}
										<x-badge
											class="ms-2 text-2xs"
											variant="secondary"
										>
											@lang('New')
										</x-badge>
									</h4>
									<x-forms.input
										id="onetime_commission"
										name="onetime_commission"
										type="checkbox"
										switcher
										type="checkbox"
										:checked="setting('onetime_commission', 0) == 1"
										label="{{ __('Onetime Commission') }}"
										tooltip="{{ __('If you want to give a commission to the affiliate only once, you can activate this feature. When disabled, the affiliate will receive a recurring commission for each purchase.') }}"
									/>
								</x-card>
							</div>
						</div>
                </div>
            </div>

            <div class="col-md-5 mx-auto">
				<x-form-step
					class="mb-4 mt-5"
					step="2"
					label="{{ __('Billing') }}"
				>
				</x-form-step>
				<div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Invoice Name') }}</label>
                            <input
                                    class="form-control"
                                    id="invoice_name"
                                    type="text"
                                    name="invoice_name"
                                    value="{{ $setting->invoice_name }}"
                            >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Invoice Website') }}</label>
                            <input
                                    class="form-control"
                                    id="invoice_website"
                                    type="text"
                                    name="invoice_website"
                                    value="{{ $setting->invoice_website }}"
                            >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Invoice Address') }}</label>
                            <textarea
                                    class="form-control"
                                    id="invoice_address"
                                    type="text"
                                    name="invoice_address"
                            >{{ $setting->invoice_address }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Invoice City') }}</label>
                            <input
                                    class="form-control"
                                    id="invoice_city"
                                    type="text"
                                    name="invoice_city"
                                    value="{{ $setting->invoice_city }}"
                            >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Invoice State') }}</label>
                            <input
                                    class="form-control"
                                    id="invoice_state"
                                    type="text"
                                    name="invoice_state"
                                    value="{{ $setting->invoice_state }}"
                            >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Invoice Postal') }}</label>
                            <input
                                    class="form-control"
                                    id="invoice_postal"
                                    type="text"
                                    name="invoice_postal"
                                    value="{{ $setting->invoice_postal }}"
                            >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Invoice Country') }}</label>
                            <input
                                    class="form-control"
                                    id="invoice_country"
                                    type="text"
                                    name="invoice_country"
                                    value="{{ $setting->invoice_country }}"
                            >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Invoice Phone') }}</label>
                            <input
                                    class="form-control"
                                    id="invoice_phone"
                                    type="text"
                                    name="invoice_phone"
                                    value="{{ $setting->invoice_phone }}"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-11 mt-4 mx-auto">
                <button
                        class="btn btn-primary w-full"
                        type="submit"
                >
                    {{ __('Save') }}
                </button>
            </div>
        </div>

    </form>
@endsection


@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>
@endpush
