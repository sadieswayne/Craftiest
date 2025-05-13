@if($app_is_demo || (($setting->feature_affilates ?? true) && (\auth()->user()?->affiliate_status === 1)))
	@php
		$totalEarning = cache('total_earnings') ?: 0;
	@endphp
	<x-card class="flex flex-col w-full lg:w-[48%]" id="invite-friend" size="md">
		<div class="rounded-xl dark:bg-foreground/5 bg-[#F3F3F3] p-4 w-fit">
			<svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path
					d="M26.4595 16.7999H1.65428V24.3596C1.65524 25.4733 2.09117 26.5411 2.86639 27.3286C3.64161 28.1162 4.69276 28.559 5.78908 28.56H22.3247C23.421 28.559 24.4721 28.1162 25.2474 27.3286C26.0226 26.5411 26.4585 25.4733 26.4595 24.3596V16.7999ZM11.5764 26.0401C11.5764 26.263 11.4892 26.4767 11.3341 26.6343C11.179 26.7918 10.9686 26.8804 10.7492 26.8804C10.5298 26.8804 10.3195 26.7918 10.1643 26.6343C10.0092 26.4767 9.92208 26.263 9.92208 26.0401V19.3198C9.92208 19.0969 10.0092 18.8832 10.1643 18.7256C10.3195 18.568 10.5298 18.4795 10.7492 18.4795C10.9686 18.4795 11.179 18.568 11.3341 18.7256C11.4892 18.8832 11.5764 19.0969 11.5764 19.3198V26.0401ZM18.1917 26.0401C18.1917 26.263 18.1045 26.4767 17.9494 26.6343C17.7943 26.7918 17.5839 26.8804 17.3645 26.8804C17.1452 26.8804 16.9348 26.7918 16.7797 26.6343C16.6245 26.4767 16.5374 26.263 16.5374 26.0401V19.3198C16.5374 19.0969 16.6245 18.8832 16.7797 18.7256C16.9348 18.568 17.1452 18.4795 17.3645 18.4795C17.5839 18.4795 17.7943 18.568 17.9494 18.7256C18.1045 18.8832 18.1917 19.0969 18.1917 19.3198V26.0401ZM23.979 6.72032H20.6505C21.1737 6.01659 21.4984 5.14974 21.4984 4.20043C21.4975 3.0867 21.0615 2.01887 20.2863 1.23135C19.5111 0.443826 18.46 0.000970568 17.3636 0C16.007 0 14.811 0.67808 14.0569 1.7062C13.6752 1.17926 13.1773 0.750582 12.603 0.454585C12.0287 0.158587 11.3941 0.00351906 10.7501 0.00183252C9.65411 0.00280228 8.60323 0.445406 7.82806 1.23253C7.0529 2.01966 6.61675 3.08702 6.61532 4.20043C6.61532 5.14974 6.94004 6.01475 7.4632 6.72032H4.1348C3.03848 6.72129 1.98733 7.16415 1.21211 7.95167C0.436891 8.7392 0.000955403 9.80703 0 10.9208V12.5995C0 13.9904 1.11308 15.1194 2.48052 15.1194H25.6332C27.0007 15.1194 28.1137 13.9904 28.1137 12.5995V10.9208C28.1128 9.80703 27.6769 8.7392 26.9016 7.95167C26.1264 7.16415 25.0753 6.72129 23.979 6.72032ZM11.5764 12.5995C11.5764 12.8223 11.4892 13.036 11.3341 13.1936C11.179 13.3512 10.9686 13.4397 10.7492 13.4397C10.5298 13.4397 10.3195 13.3512 10.1643 13.1936C10.0092 13.036 9.92208 12.8223 9.92208 12.5995V9.24022C9.92208 9.01736 10.0092 8.80364 10.1643 8.64606C10.3195 8.48847 10.5298 8.39995 10.7492 8.39995C10.9686 8.39995 11.179 8.48847 11.3341 8.64606C11.4892 8.80364 11.5764 9.01736 11.5764 9.24022V12.5995ZM13.2306 6.72032H10.7501C10.0924 6.71984 9.46174 6.45419 8.99665 5.98173C8.53157 5.50926 8.27008 4.8686 8.2696 4.20043C8.29407 3.54874 8.56612 2.93203 9.02863 2.47976C9.49114 2.02749 10.1081 1.77483 10.7501 1.77483C11.3921 1.77483 12.0091 2.02749 12.4716 2.47976C12.9341 2.93203 13.2062 3.54874 13.2306 4.20043V6.72032ZM18.1917 12.5995C18.1917 12.8223 18.1045 13.036 17.9494 13.1936C17.7943 13.3512 17.5839 13.4397 17.3645 13.4397C17.1452 13.4397 16.9348 13.3512 16.7797 13.1936C16.6245 13.036 16.5374 12.8223 16.5374 12.5995V9.24022C16.5374 9.01736 16.6245 8.80364 16.7797 8.64606C16.9348 8.48847 17.1452 8.39995 17.3645 8.39995C17.5839 8.39995 17.7943 8.48847 17.9494 8.64606C18.1045 8.80364 18.1917 9.01736 18.1917 9.24022V12.5995ZM17.3636 6.72032H14.8831V4.20043C14.8831 2.81128 15.9962 1.68054 17.3636 1.68054C18.7311 1.68054 19.8442 2.81128 19.8442 4.20043C19.8442 5.58958 18.7311 6.72032 17.3636 6.72032Z"
					fill="#9A34CD" />
			</svg>
		</div>
		<div class="flex flex-col gap-3 pt-4">
			<p class="mb-4 text-xl font-semibold text-foreground">
				@lang('Invite your friends and earn lifelong recurring commissions. 💸')
			</p>
			<span class="text-[14px] leading-5 text-foreground/80">@lang('Simply share your referral link and have your friends sign up through it.')</span>
			<div class="flex items-center gap-3 max-sm:flex-wrap">
				<p class="mb-0">
                <span class="opacity-60">
                    {{ __('Commission Rate') }}:
                </span>
					{{ $setting->affiliate_commission_percentage }}%
				</p>
				<span class="h-1 w-1 rounded-full bg-foreground\/10 max-sm:hidden"></span>
				<p class="mb-0">
                <span class="opacity-60">
                    {{ __('Referral Program') }}:
                </span>
					@if ($is_onetime_commission)
						{{ __('First Purchase') }}
					@else
						{{ __('All Purchases') }}
					@endif
				</p>
			</div>
		</div>
		<div class="flex flex-col gap-6 sm:pt-7">
			<x-card class="w-full" class:body="flex justify-between items-center" id="earning" size="sm">
				<span class="text-[14px] text-foreground font-semibold">@lang('Earnings')</span>
				<span class="text-lg font-semibold">{{ currency()->symbol }}<strong class="text-[30px]">{{ $totalEarning ?? 0 }}</strong></span>
			</x-card>
			<x-forms.input id="invite-url" size="lg" type="text" name="invite-url"
						   value="{{ str()->limit(LaravelLocalization::localizeUrl(url('/') . '/register?aff=' . \Illuminate\Support\Facades\Auth::user()->affiliate_code), 60) }}"
						   disabled>
				<x-slot:icon>
					<x-tabler-copy class="absolute cursor-pointer end-3 top-1/2 size-5 -translate-y-1/2"
								   onclick="navigator.clipboard.writeText(document.getElementById('invite-url').value); toastr.success('{{ __('Copied to clipboard!') }}');" />
				</x-slot:icon>
			</x-forms.input>
		</div>
	</x-card>
@endif
