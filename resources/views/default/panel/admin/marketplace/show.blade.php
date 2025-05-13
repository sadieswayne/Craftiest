@php
	$price_details = [
		[
			'label' => 'Free updates',
			'value' => 'Lifetime',
		],
		[
			'label' => 'Support',
			'value' => '6 months',
		],
		[
			'label' => 'License',
			'value' => 'Extended',
		],
		[
			'label' => 'Installation',
			'value' => 'One Click',
		],
	];
@endphp

@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Marketplace'))
@section('titlebar_pretitle')
	<x-button
		class="text-inherit hover:text-foreground"
		variant="link"
		href="{{ route('dashboard.admin.marketplace.index') }}"
	>
		<x-tabler-chevron-left
			class="size-4"
			stroke-width="1.5"
		/>
		{{ __('Back to Marketplace') }}
	</x-button>
@endsection
@section('titlebar_actions')
	<div class="flex flex-wrap gap-2">
		<x-button
			variant="ghost-shadow"
			href="{{ route('dashboard.admin.marketplace.liextension') }}"
		>
			{{ __('Manage Addons') }}
		</x-button>
		<x-button href="{{ route('dashboard.admin.marketplace.index') }}">
			<x-tabler-plus class="size-4"/>
			{{ __('Browse Add-ons') }}
		</x-button>
		<x-button
			class="relative ms-2"
			variant="ghost-shadow"
			href="{{ route('dashboard.admin.marketplace.cart') }}">
			<x-tabler-shopping-cart class="size-4"/>
			{{ __('Cart') }}
			<small id="itemCount"
				   class="bg-red-500 text-white ps-2 pe-2 absolute top-[-10px] right-[3px] rounded-[50%] border border-red-500">{{ count(is_array($cart) ? $cart : []) }}</small>
		</x-button>
	</div>
@endsection

@section('content')
	<div class="py-10">
		<div class="lqd-extension-details flex flex-col justify-between gap-y-7 md:flex-row">
			<x-card
				class="lqd-extension-details-card relative w-full pb-10 lg:w-8/12 [&_hr]:my-5 [&_hr]:border-border"
				variant="shadow"
				size="lg"
			>
				<img
					class="size-[90px] mb-8"
					src="{{ $item['icon'] }}"
				>

				<div class="mb-8 flex flex-wrap items-center gap-2">
					<h3 class="m-0 text-[23px] font-semibold">
						{{ $item['name'] }}
					</h3>

					@if ($item['installed'] and $item['version'] == $item['db_version'])
						<p class="mb-0 ms-3 flex items-center gap-2 text-2xs font-medium">
							<span class="size-2 inline-block rounded-full bg-green-500"></span>
							{{ __('Installed') }}
						</p>
					@else
						<a class="mb-0 ms-3 flex items-center gap-2 text-2xs font-medium">
							<span class="size-2 inline-block rounded-full bg-green-500"></span>
							{{ __('Upgrade') }}
						</a>
					@endif
				</div>

				<div class="mb-10 flex flex-wrap items-center gap-6 text-sm font-medium text-heading-foreground">
					<div class="flex items-center gap-1.5">
						{{-- blade-formatter-disable --}}
						<svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M16.7619 7.99999L14.9028 5.87428L15.1619 3.06285L12.4114 2.43809L10.9714 0L8.38094 1.11238L5.79047 0L4.35047 2.43047L1.6 3.04762L1.85905 5.86666L0 7.99999L1.85905 10.1257L1.6 12.9447L4.35047 13.5695L5.79047 16L8.38094 14.88L10.9714 15.9924L12.4114 13.5619L15.1619 12.9371L14.9028 10.1257L16.7619 7.99999ZM6.92571 11.5962L4.03047 8.69332L5.15809 7.56571L6.92571 9.34094L11.3828 4.86857L12.5105 5.99618L6.92571 11.5962Z"
								fill="#347AE2"/>
						</svg>
						{{-- blade-formatter-enable --}}
						<p class="m-0">
							{{ __('Tested with MagicAI') }}
						</p>
					</div>

					<span class="inline-block h-5 w-px bg-foreground/10"></span>

					<p class="review m-0 flex items-center gap-1">
						<x-tabler-star-filled class="size-3"/>
						{{ number_format($item['review'], 1) }}
					</p>

					<span class="inline-block h-5 w-px bg-foreground/10"></span>

					<div class="flex items-center gap-2">
						{{-- blade-formatter-disable --}}
						<svg width="18" height="18" viewBox="0 0 18 18" fill="currentColor"
							 xmlns="http://www.w3.org/2000/svg">
							<path
								d="M11.7084 3.1665C11.4167 3.1665 11.1702 3.06581 10.9688 2.86442C10.7674 2.66303 10.6667 2.4165 10.6667 2.12484C10.6667 1.83317 10.7674 1.58664 10.9688 1.38525C11.1702 1.18387 11.4167 1.08317 11.7084 1.08317C12 1.08317 12.2465 1.18387 12.4479 1.38525C12.6493 1.58664 12.75 1.83317 12.75 2.12484C12.75 2.4165 12.6493 2.66303 12.4479 2.86442C12.2465 3.06581 12 3.1665 11.7084 3.1665ZM11.7084 16.9165C11.4167 16.9165 11.1702 16.8158 10.9688 16.6144C10.7674 16.413 10.6667 16.1665 10.6667 15.8748C10.6667 15.5832 10.7674 15.3366 10.9688 15.1353C11.1702 14.9339 11.4167 14.8332 11.7084 14.8332C12 14.8332 12.2465 14.9339 12.4479 15.1353C12.6493 15.3366 12.75 15.5832 12.75 15.8748C12.75 16.1665 12.6493 16.413 12.4479 16.6144C12.2465 16.8158 12 16.9165 11.7084 16.9165ZM15.0417 6.08317C14.75 6.08317 14.5035 5.98248 14.3021 5.78109C14.1007 5.5797 14 5.33317 14 5.0415C14 4.74984 14.1007 4.50331 14.3021 4.30192C14.5035 4.10053 14.75 3.99984 15.0417 3.99984C15.3334 3.99984 15.5799 4.10053 15.7813 4.30192C15.9827 4.50331 16.0834 4.74984 16.0834 5.0415C16.0834 5.33317 15.9827 5.5797 15.7813 5.78109C15.5799 5.98248 15.3334 6.08317 15.0417 6.08317ZM15.0417 13.9998C14.75 13.9998 14.5035 13.8991 14.3021 13.6978C14.1007 13.4964 14 13.2498 14 12.9582C14 12.6665 14.1007 12.42 14.3021 12.2186C14.5035 12.0172 14.75 11.9165 15.0417 11.9165C15.3334 11.9165 15.5799 12.0172 15.7813 12.2186C15.9827 12.42 16.0834 12.6665 16.0834 12.9582C16.0834 13.2498 15.9827 13.4964 15.7813 13.6978C15.5799 13.8991 15.3334 13.9998 15.0417 13.9998ZM16.2917 10.0415C16 10.0415 15.7535 9.94081 15.5521 9.73942C15.3507 9.53803 15.25 9.2915 15.25 8.99984C15.25 8.70817 15.3507 8.46164 15.5521 8.26025C15.7535 8.05886 16 7.95817 16.2917 7.95817C16.5834 7.95817 16.8299 8.05886 17.0313 8.26025C17.2327 8.46164 17.3334 8.70817 17.3334 8.99984C17.3334 9.2915 17.2327 9.53803 17.0313 9.73942C16.8299 9.94081 16.5834 10.0415 16.2917 10.0415ZM9.00002 17.3332C7.84724 17.3332 6.76391 17.1144 5.75002 16.6769C4.73613 16.2394 3.85419 15.6457 3.10419 14.8957C2.35419 14.1457 1.76044 13.2637 1.32294 12.2498C0.885437 11.2359 0.666687 10.1526 0.666687 8.99984C0.666687 7.84706 0.885437 6.76373 1.32294 5.74984C1.76044 4.73595 2.35419 3.854 3.10419 3.104C3.85419 2.354 4.73613 1.76025 5.75002 1.32275C6.76391 0.885254 7.84724 0.666504 9.00002 0.666504V2.33317C7.13891 2.33317 5.56252 2.979 4.27085 4.27067C2.97919 5.56234 2.33335 7.13873 2.33335 8.99984C2.33335 10.8609 2.97919 12.4373 4.27085 13.729C5.56252 15.0207 7.13891 15.6665 9.00002 15.6665V17.3332ZM9.00002 10.6665C8.54169 10.6665 8.14933 10.5033 7.82294 10.1769C7.49655 9.85053 7.33335 9.45817 7.33335 8.99984C7.33335 8.93039 7.33683 8.85748 7.34377 8.78109C7.35071 8.7047 7.36808 8.63178 7.39585 8.56234L5.66669 6.83317L6.83335 5.6665L8.56252 7.39567C8.61808 7.38178 8.76391 7.36095 9.00002 7.33317C9.45835 7.33317 9.85071 7.49637 10.1771 7.82275C10.5035 8.14914 10.6667 8.5415 10.6667 8.99984C10.6667 9.45817 10.5035 9.85053 10.1771 10.1769C9.85071 10.5033 9.45835 10.6665 9.00002 10.6665Z"/>
						</svg>
						{{-- blade-formatter-enable --}}
						<p class="m-0">
							{{ __('Recently Updated') }}
						</p>
					</div>
				</div>

				<h3 class="mb-7">
					{{ __('About this add-on') }}
				</h3>

				<div class="mb-8">
					{!! $item['detail'] !!}
				</div>

				<div class="mb-11 flex flex-col gap-4">
					@foreach ($item['categories'] as $tag)
						<p class="flex items-center gap-3 text-base font-medium">
                            <span
								class="size-6 me-1 inline-flex items-center justify-center rounded-xl bg-primary/[8%] align-middle text-primary dark:bg-secondary/15 dark:text-secondary-foreground"
							>
                                <x-tabler-check class="size-3.5"/>
                            </span>
							{{ $tag }}
						</p>
					@endforeach
				</div>

				@if (!empty($item['questions']) && !$item['only_show'])
					<div>
						<h3 class="mb-7">
							{{ __('Have a question?') }}
						</h3>
						<div
							class="lqd-accordion flex flex-col gap-4"
							x-data="{ activeIndex: 0 }"
						>
							@foreach ($item['questions'] as $qaItem)
								<div
									class="lqd-accordion-item rounded-2xl border [&.lqd-is-active]:shadow-lg [&.lqd-is-active]:shadow-black/5"
									data-index="{{ $loop->index }}"
									:class="{ 'lqd-is-active': activeIndex == '{{ $loop->index }}' }"
								>
									<button
										class="lqd-accordion-trigger flex w-full items-center justify-between gap-4 px-7 py-4 text-start text-base font-semibold leading-tight text-heading-foreground"
										data-index="{{ $loop->index }}"
										@click.prevent="activeIndex = activeIndex == '{{ $loop->index }}' ? null : '{{ $loop->index }}'"
									>
										{{ __($qaItem['question']) }}
										<x-tabler-chevron-down class="size-5 ms-auto shrink-0"/>
									</button>
									<div
										@class([
											'lqd-accordion-content px-7',
											'hidden' => $loop->index != 0,
											'lqd-is-active' => $loop->index == 0,
										])
										:class="{ 'hidden': activeIndex != {{ $loop->index }} }"
									>
										<div class="lqd-accordion-content-inner pb-4">
											<p class="text-sm leading-relaxed opacity-80">
												{{ $qaItem['answer'] }}
											</p>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					</div>
				@endif
			</x-card>

			<div class="flex w-full flex-col gap-8 lg:w-4/12 lg:ps-8">
				<x-card
					class="lqd-extension-price-card text-center"
					size="lg"
				>
					@if(! $item['only_show'])
						<h4 class="mb-6">
							{{ __('Limited Offer') }}
						</h4>
					@endif


					@if ($item['price'] != 0 && (! $item['only_show']))
						<div class="mb-5">
							<p class="text-4xl font-semibold leading-none">
								@if (currencyShouldDisplayOnRight(currency()['symbol']))
									{{ $item['price'] }} $
									@if ($item['fake_price'] ?? false)
										<small
											class="text-[18px] line-through"
											style="text-decoration: line-through;"
										>{{ $item['fake_price'] }}$</small>
									@endif
								@else
									${{ $item['price'] }}
									@if ($item['fake_price'] ?? false)
										<small
											class="text-[18px] line-through"
											style="text-decoration: line-through;"
										>${{ $item['fake_price'] }} </small>
									@endif
								@endif
							</p>
							<p class="m-0 text-2xs font-semibold text-primary/50">
								{{ __('For a limited time only') }}
							</p>
						</div>

						<p class="mb-6 text-2xs opacity-60">
							{{ __('Price is in US dollars. Tax included.') }}
						</p>
					@else
						<p class="mb-5 text-4xl font-semibold leading-none text-heading-foreground">
							@if(! $item['only_show'])
								{{ __('Free') }}

							@else
								{{ __('Contact Us') }}
							@endif
						</p>
					@endif




					@if(! $item['only_show'])
						<div class="flex justify-{{ $item['is_buy'] ? 'between' : 'center'}}">
							@if ($item['price'] != 0)
								@if ($app_is_demo)
									<x-button
										class="w-full"
										size="lg"
										href="#"
										onclick="return toastr.info('This feature is disabled in Demo version.')"
										disabled
									>
										{{ __('Buy Now') }}
									</x-button>
								@else
									@php
										$is_license = $item['licensed'] == 1;
									@endphp

									@if($is_license)
										<x-button
											class="w-full"
											size="lg"
											href="{{ route('dashboard.admin.marketplace.liextension') }}"
										>
											{{ __('Install Now') }}
										</x-button>
									@else
										@if($item['is_buy'])




											@if($item['slug'] === 'chatbot-agent')

												@if(\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot'))
													<x-button
														target="_blank"
														class="w-full"
														size="lg"
														href="{{ $item['routes']['payment'] }}"
													>
														{{ __('Buy Now') }}
													</x-button>
												@else

													<x-button
														class="w-full"
														size="lg"
														href="#"
														onclick="return toastr.info('External Chatbot is required for this extension.')"
													>
														{{ __('Buy Now') }}
													</x-button>
												@endif

											@else
												<x-button
													target="_blank"
													class="w-full"
													size="lg"
													href="{{ $item['routes']['payment'] }}"
												>
													{{ __('Buy Now') }}
												</x-button>
											@endif

										@else
											<span
												class="lqd-tooltip-container group relative inline-flex cursor-default before:absolute before:-start-1.5 before:-top-1.5 before:h-7 before:w-7">
                                                    <span class="lqd-tooltip-icon ">
                                                         <x-button
															 target="_blank"
															 class="w-full lqd-tooltip-icon "
															 size="lg"
															 type="button"
														 >
                                                                {{ __('Learn more') }}
                                                            </x-button>
                                                    </span>
                                                    <span
														class="lqd-tooltip-content min-w-64 invisible absolute bottom-full start-1/2 z-50 mb-3 -translate-x-1/2 translate-y-1 rounded-xl bg-background/80 px-4 py-3  text-xs leading-normal text-foreground opacity-0 shadow-lg shadow-black/5 backdrop-blur-sm backdrop-saturate-150 transition-all before:absolute before:inset-x-0 before:-bottom-3 before:h-3 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"
													>
                                                            Contact us for more information:
                                                           <span class="flex justify-center mt-1">info@liquid-themes.com <x-tabler-copy class="ms-2"
																																		id="copyButton"/></span>

                                                    </span>
                                                </span>
										@endif
									@endif

								@endif
							@else
								@if ($item['installed'])
									@if ($item['version'] == $item['db_version'])
										<x-button
											class="w-full"
											size="lg"
											href="#"
											variant="outline"
										>
											{{ __('Installed') }}
										</x-button>
									@else
										<x-button
											class="w-full"
											size="lg"
											href="{{ route('dashboard.admin.marketplace.liextension') }}"
										>
											{{ __('Upgrade') }}
										</x-button>
									@endif
								@else
									<x-button
										class="w-full"
										size="lg"
										href="{{ route('dashboard.admin.marketplace.liextension') }}"
									>
										{{ __('Install Now') }}
									</x-button>
								@endif

							@endif
							@if((! $item['licensed']) && $item['price'] && $item['is_buy'])
									@if($item['slug'] === 'chatbot-agent')

										@if(\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot'))

											<x-button
												data-toogle="cart"
												data-url="{{ route('dashboard.admin.marketplace.cart.add-delete', $item['id']) }}"
												class="relative ms-2"
												variant="ghost-shadow"
												href="#">
												<x-tabler-shopping-cart id="{{ $item['id'].'-icon' }}"
																		class="size-7 text-{{ in_array($item['id'], $cartExists) ? 'green' : 'gray' }}-500"/>
											</x-button>

										@else

											<x-button
												onclick="return toastr.info('External Chatbot is required for this extension.')"
												class="relative ms-2"
												variant="ghost-shadow"
												href="#">
												<x-tabler-shopping-cart
													id="{{ $item['id'].'-icon' }}"
													class="size-7 text-{{ in_array($item['id'], $cartExists) ? 'green' : 'gray' }}-500"
												/>
											</x-button>

										@endif
									@else
										<x-button
											data-toogle="cart"
											data-url="{{ route('dashboard.admin.marketplace.cart.add-delete', $item['id']) }}"
											class="relative ms-2"
											variant="ghost-shadow"
											href="#">
											<x-tabler-shopping-cart id="{{ $item['id'].'-icon' }}"
																	class="size-7 text-{{ in_array($item['id'], $cartExists) ? 'green' : 'gray' }}-500"/>
										</x-button>
									@endif

							@endif
						</div>
					@else
						<x-button
							class="w-full"
							size="lg"
							target="_blank"
							href="{!! $item['routes']['redirect'] ? $item['routes']['redirect'] .'&email='.auth()->user()->email()  : 'https://mobile.magicproject.ai' !!} "
						>
							{{ __('Live Preview') }}
						</x-button>
					@endif


				</x-card>
				@if (!$marketSubscription['data'] && $item['price'] != 0 && \App\Helpers\Classes\Helper::appIsNotDemo())

					<div class="flex items-center gap-7 text-2xs text-heading-foreground">
						<span class="block h-px grow bg-heading-foreground/10"></span>
						{{ trans('or') }}
						<span class="block h-px grow bg-heading-foreground/10"></span>
					</div>

					<x-card
						class="lqd-extension-limited-offer text-center"
						size="lg"
					>
						<h6 class="mb-6 inline-block rounded-full bg-[#F5FAFF] px-4 py-1 font-body text-sm font-semibold">
                            <span class="inline-block bg-gradient-to-br from-[#82E2F4] via-[#8A8AED] to-[#6977DE] bg-clip-text text-transparent">
                                {{ trans('Limited Time Offer') }}
                            </span>
						</h6>

						<h3 class="mb-6 text-[22px] font-bold">
							{{ trans('Included with ') }}
							<span class="inline-block bg-gradient-to-br from-[#82E2F4] via-[#8A8AED] to-[#6977DE] bg-clip-text text-transparent">
                                {{ trans('Premium Membership.') }}
                            </span>
						</h3>

						<p class="mb-8 font-medium">
							{{ trans('Join our exclusive membership to access to all extensions in the marketplace. ') }}
						</p>

						<ul class="mx-auto mb-10 flex flex-col gap-2 text-start text-base font-semibold text-heading-foreground lg:w-11/12">
							@foreach (['VIP Support', '5 Hours of Customization', 'Access to All Extensions'] as $item)
								<li class="flex items-center gap-3.5">
									<svg
										class="shrink-0"
										width="16"
										height="15"
										viewBox="0 0 16 15"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
									>
										<path
											d="M2.09635 6.87072C1.80296 6.87154 1.51579 6.95542 1.26807 7.11264C1.02035 7.26986 0.822208 7.494 0.696564 7.75914C0.570919 8.02427 0.522908 8.31956 0.558084 8.61084C0.59326 8.90212 0.710186 9.17749 0.895335 9.4051L4.84228 14.2401C4.98301 14.4148 5.1634 14.5535 5.36847 14.6445C5.57353 14.7355 5.79736 14.7763 6.02136 14.7635C6.50043 14.7377 6.93295 14.4815 7.20871 14.0601L15.4075 0.855925C15.4089 0.853735 15.4103 0.851544 15.4117 0.849387C15.4886 0.731269 15.4637 0.497192 15.3049 0.350142C15.2613 0.309761 15.2099 0.278736 15.1538 0.25898C15.0977 0.239223 15.0382 0.231153 14.9789 0.235266C14.9196 0.239379 14.8618 0.255589 14.809 0.282896C14.7562 0.310204 14.7095 0.348031 14.6719 0.394048C14.669 0.397666 14.6659 0.40123 14.6628 0.404739L6.39421 9.74702C6.36275 9.78257 6.32454 9.81152 6.28179 9.83218C6.23905 9.85283 6.19263 9.86479 6.14522 9.86736C6.09782 9.86992 6.05038 9.86304 6.00565 9.84711C5.96093 9.83119 5.91982 9.80653 5.88471 9.77458L3.14051 7.27735C2.8555 7.01608 2.48299 6.87102 2.09635 6.87072Z"
											fill="url(#paint0_linear_6413_808)"
										/>
										<defs>
											<linearGradient
												id="paint0_linear_6413_808"
												x1="0.546875"
												y1="3.19866"
												x2="12.7738"
												y2="14.2613"
												gradientUnits="userSpaceOnUse"
											>
												<stop stop-color="#82E2F4"/>
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
									{!! trans($item) !!}

								</li>
							@endforeach

							<li>
								<div class="-mx-6 flex flex-col items-start lg:-mx-8">
                                    <span
										class="ms-6 inline-block border-b-[10px] border-e-[10px] border-s-[10px] border-t-[10px] border-transparent border-b-heading-foreground/5 lg:ms-8"
									></span>
									<ul class="flex w-full flex-col gap-2.5 rounded-xl bg-heading-foreground/5 px-6 py-5">
										@foreach ($paidExtensions as $item)
											<li class="flex items-center gap-3.5">
												<svg
													width="16"
													height="15"
													viewBox="0 0 16 15"
													fill="none"
													xmlns="http://www.w3.org/2000/svg"
												>
													<path
														d="M2.09635 6.87072C1.80296 6.87154 1.51579 6.95542 1.26807 7.11264C1.02035 7.26986 0.822208 7.494 0.696564 7.75914C0.570919 8.02427 0.522908 8.31956 0.558084 8.61084C0.59326 8.90212 0.710186 9.17749 0.895335 9.4051L4.84228 14.2401C4.98301 14.4148 5.1634 14.5535 5.36847 14.6445C5.57353 14.7355 5.79736 14.7763 6.02136 14.7635C6.50043 14.7377 6.93295 14.4815 7.20871 14.0601L15.4075 0.855925C15.4089 0.853735 15.4103 0.851544 15.4117 0.849387C15.4886 0.731269 15.4637 0.497192 15.3049 0.350142C15.2613 0.309761 15.2099 0.278736 15.1538 0.25898C15.0977 0.239223 15.0382 0.231153 14.9789 0.235266C14.9196 0.239379 14.8618 0.255589 14.809 0.282896C14.7562 0.310204 14.7095 0.348031 14.6719 0.394048C14.669 0.397666 14.6659 0.40123 14.6628 0.404739L6.39421 9.74702C6.36275 9.78257 6.32454 9.81152 6.28179 9.83218C6.23905 9.85283 6.19263 9.86479 6.14522 9.86736C6.09782 9.86992 6.05038 9.86304 6.00565 9.84711C5.96093 9.83119 5.91982 9.80653 5.88471 9.77458L3.14051 7.27735C2.8555 7.01608 2.48299 6.87102 2.09635 6.87072Z"
														fill="url(#paint0_linear_6413_808)"
													/>
													<defs>
														<linearGradient
															id="paint0_linear_6413_808"
															x1="0.546875"
															y1="3.19866"
															x2="12.7738"
															y2="14.2613"
															gradientUnits="userSpaceOnUse"
														>
															<stop stop-color="#82E2F4"/>
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
												{!! trans($item['name']) !!}
											</li>
										@endforeach
									</ul>
								</div>
							</li>
						</ul>

						<x-button
							class="mx-auto w-full py-3.5 shadow-lg shadow-black/10 lg:w-11/12"
							size="lg"
							target="_blank"
							href="{{ $marketSubscription['extensionPayment'] }}"
						>
							{{ trans('Join Premium Membership') }}
						</x-button>
					</x-card>
				@endif

				@if(!$item['only_show'])
					<x-card
						class="lqd-extension-price-details"
						size="lg"
					>
						<h4 class="mb-6 border-b pb-3">
							{{ __('Details') }}
						</h4>
						<div class="grid grid-cols-2 gap-4">
							@foreach ($price_details as $detail)
								<div class="lqd-extension-price-detail flex flex-col rounded-xl border px-4 py-3 font-semibold">
									<p class="mb-6 text-4xs uppercase tracking-widest text-heading-foreground opacity-70">
										{{ __($detail['label']) }}
									</p>
									<p class="mt-auto text-heading-foreground">
										{{ __($detail['value']) }}
									</p>
								</div>
							@endforeach
						</div>
					</x-card>
				@endif

			</div>


		</div>
	</div>
@endsection

@push('script')
	<script src="{{ custom_theme_url('/assets/js/panel/marketplace.js') }}"></script>
	<script>
		document.getElementById('copyButton').addEventListener('click', function () {
			navigator.clipboard.writeText('info@liquid-themes.com');
			toastr.success(@json(__('Copied')));
		});
	</script>
@endpush
