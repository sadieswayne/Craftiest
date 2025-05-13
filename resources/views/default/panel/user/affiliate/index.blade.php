@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Affiliate'))
@section('titlebar_actions')
    <x-button
        variant="primary"
        href="{{  (route('dashboard.user.affiliates.users')) }}"
    >
        {{ __('Affilated Users') }}
    </x-button>
@endsection

@section('content')
    <div class="pt-6">
        <div class="flex flex-wrap justify-between gap-y-8">
            <x-card
                class="lqd-affiliate-overview bg-gradient-to-b from-blue-400/30 to-transparent shadow-sm"
                size="lg"
            >
                <div class="flex flex-wrap gap-y-8">
                    <div class="w-full md:w-5/12">
                        <h4 class="mb-10 w-10/12 text-xl">
                            {{ __('Invite your friends and earn lifelong recurring commissions from every purchase they make') }}.🎁
                        </h4>
                        <p class="mb-2 text-2xs text-heading-foreground">
                            {{ __('Affiliate Link') }}
                        </p>

                        <div class="relative">
                            <x-forms.input
                                class="hidden"
                                id="ref-code"
                                disabled
                                value="{{  (url('/') . '/register?aff=' . \Illuminate\Support\Facades\Auth::user()->affiliate_code) }}"
                            />
                            <x-forms.input
                                class="h-10 bg-background"
                                disabled
                                value="{{ str()->limit( (url('/') . '/register?aff=' . \Illuminate\Support\Facades\Auth::user()->affiliate_code), 60) }}"
                            />
                            <x-button
                                class="copy-aff-link absolute end-0 top-0 inline-flex h-full w-9 items-center rounded-input bg-transparent text-heading-foreground hover:bg-emerald-400 hover:text-white"
                                variant="link"
                                size="none"
                            >
                                <x-tabler-copy class="size-4" />
                            </x-button>
                        </div>
                    </div>

                    <div class="ms-auto w-full text-center font-semibold text-heading-foreground max-md:-order-1 max-md:mb-3 max-md:!text-start md:w-4/12">
                        <h4 class="mb-0 text-base">
                            {{ __('Earnings') }}
                        </h4>

                        <p class="mb-2 text-6xl">
                            @if (currencyShouldDisplayOnRight(currency()->symbol))
                                {{ $totalEarnings - $totalWithdrawal }}{{ currency()->symbol }}
                            @else
                                {{ currency()->symbol }}{{ $totalEarnings - $totalWithdrawal }}
                            @endif
                        </p>

                        <p class="mb-0">
                            <span class="opacity-60">
                                {{ __('Commission Rate') }}:
                            </span>
                            {{ $setting->affiliate_commission_percentage }}%
                        </p>

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
            </x-card>

            <x-card class="lqd-affiliate-form w-full lg:w-[48%]">
                <h2 class="mb-6">
                    {{ __('How it Works') }}
                </h2>

                <ol class="mb-12 flex flex-col gap-4 text-heading-foreground">
                    <li>
                        <span class="me-2 inline-flex size-7 items-center justify-center rounded-full bg-primary/10 font-extrabold text-primary">
                            1
                        </span>
                        {!! __('You <strong>send your invitation link</strong> to your friends.') !!}
                    </li>
                    <li>
                        <span class="me-2 inline-flex size-7 items-center justify-center rounded-full bg-primary/10 font-extrabold text-primary">
                            2
                        </span>
                        {!! __('<strong>They subscribe</strong> to a paid plan by using your refferral link.') !!}
                    </li>
                    <li>
                        <span class="me-2 inline-flex size-7 items-center justify-center rounded-full bg-primary/10 font-extrabold text-primary">
                            3
                        </span>
                        @if ($is_onetime_commission)
                            {!! __('From their first purchase, you will begin <strong>earning one-time commissions</strong>.') !!}
                        @else
                            {!! __('From their first purchase, you will begin <strong>earning recurring commissions</strong>.') !!}
                        @endif
                    </li>
                </ol>

                <form
                    class="flex flex-col gap-3"
                    id="send_invitation_form"
                    onsubmit="return sendInvitationForm();"
                >
                    <x-forms.input
                        class:label="text-heading-foreground"
                        id="to_mail"
                        label="{{ __('Affiliate Link') }}"
                        size="sm"
                        type="email"
                        name="to_mail"
                        placeholder="{{ __('Email address') }}"
                        required
                    >
                        <x-slot:icon>
                            <x-tabler-mail class="absolute end-3 top-1/2 size-5 -translate-y-1/2" />
                        </x-slot:icon>
                    </x-forms.input>

                    <x-button
                        class="w-full rounded-xl"
                        id="send_invitation_button"
                        type="submit"
                        form="send_invitation_form"
                    >
                        {{ __('Send') }}
                    </x-button>
                </form>
            </x-card>

            <x-card class="lqd-affiliate-withdrawal w-full lg:w-[48%]">
                <h2 class="mb-6">
                    {{ __('Withdrawal Form') }}
                </h2>

                <form
                    class="flex flex-col gap-5"
                    id="send_request_form"
                    onsubmit="return sendRequestForm();"
                >
                    <x-forms.input
                        id="affiliate_bank_account"
                        label="{{ __('Your Bank Information') }}"
                        type="textarea"
                        rows="2"
                        name="affiliate_bank_account"
                        placeholder="{{ __('Bank of America - 2382372329 3843749 2372379') }}"
                    >{{ Auth::user()->affiliate_bank_account ?? null }}</x-forms.input>

                    <x-forms.input
                        id="amount"
                        label="{{ __('Amount') }}"
                        type="number"
                        name="amount"
                        min="{{ $setting->affiliate_minimum_withdrawal }}"
                        placeholder="{{ __('Minimum Withdrawal Amount is') }} {{ $setting->affiliate_minimum_withdrawal }}"
                    />

                    <x-button
                        class="w-full rounded-xl"
                        id="send_request_button"
                        type="submit"
                    >
                        {{ __('Send Request') }}
                    </x-button>
                </form>
            </x-card>

            <h2 class="-mb-2 w-full">
                {{ __('Withdrawal Requests') }}
            </h2>

            <x-table class="lqd-affiliate-withdrawals-table">
                <x-slot:head>
                    <tr>
                        <th>
                            {{ __('No') }}
                        </th>
                        <th>
                            {{ __('Amount') }}
                        </th>
                        <th>
                            {{ __('Status') }}
                        </th>
                        <th>
                            {{ __('Date') }}
                        </th>
                    </tr>
                </x-slot:head>
                <x-slot:body
                    class="font-medium"
                >
                    @forelse ($list2 as $entry)
                        <tr>
                            <td>
                                AFF-WTHDRWL-{{ $entry->id }}
                            </td>
                            <td>
                                {{ $entry->amount }}
                            </td>
                            <td>
                                {{ $entry->status }}
                            </td>
                            <td>
                                {{ $entry->created_at }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                class="text-center"
                                colspan="4"
                            >
                                {{ __('You have no withdrawal request') }}
                            </td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-table>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/affiliate.js') }}"></script>
@endpush
