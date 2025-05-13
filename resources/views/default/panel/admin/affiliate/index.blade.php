@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Affiliate Requests'))
@section('titlebar_actions', '')

@section('content')
    <div class="py-10">
        <h2 class="mb-6">
            {{ __('Withdrawal Requests') }}
        </h2>

        <x-table>
            <x-slot:head>
                <tr>
                    <th>#</th>
                    <th>
                        {{ __('Amount') }}
                    </th>
                    <th>
                        {{ __('Bank Information') }}
                    </th>
                    <th>
                        {{ __('Status') }}
                    </th>
                    <th>
                        {{ __('Date') }}
                    </th>
                    <th class="text-end">
                        {{ __('Action') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @forelse ($list as $entry)
                    <tr>
                        <td>
                            AFF-WTHDRWL-{{ $entry->id }}
                        </td>
                        <td>
                            {{ $entry->amount }}
                        </td>
                        <td>
                            {{ $entry->user->affiliate_bank_account }}
                        </td>
                        <td>
                            {{ $entry->status }}
                        </td>
                        <td>
                            <p class="m-0">
                                {{ date('j.n.Y', strtotime($entry->created_at)) }}
                                <span class="block opacity-60">
                                    {{ date('H:i:s', strtotime($entry->created_at)) }}
                                </span>
                            </p>
                        </td>
                        <td>
                            <x-button
                                variant="success"
                                href="{{ route('dashboard.admin.affiliates.sent', $entry->id) }}"
                            >
                                {{ __('Set as Sent') }}
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            class="text-center"
                            colspan="6"
                        >
                            {{ __('There is no withdrawal request') }}
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-table>

        <hr class="my-10">

        <h2 class="mb-6">
            {{ __('Succesfull Withdrawal Requests') }}
        </h2>

        <x-table>
            <x-slot:head>
                <tr>
                    <th>#</th>
                    <th>
                        {{ __('Amount') }}
                    </th>
                    <th>
                        {{ __('Bank Information') }}
                    </th>
                    <th>
                        {{ __('Status') }}
                    </th>
                    <th>
                        {{ __('Date') }}
                    </th>
                    <th>
                        {{ __('Action') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @forelse ($list2 as $entry)
                    <tr>
                        <td>AFF-WTHDRWL-{{ $entry->id }}</td>
                        <td>{{ $entry->amount }}</td>
                        <td>{{ $entry->user->affiliate_bank_account }}</td>
                        <td>{{ $entry->status }}</td>
                        <td>
                            <p class="m-0">
                                {{ date('j.n.Y', strtotime($entry->created_at)) }}
                                <span class="block opacity-60">
                                    {{ date('H:i:s', strtotime($entry->created_at)) }}
                                </span>
                            </p>
                        </td>
                        <td>
                            <x-button
                                variant="success"
                                href="{{ route('dashboard.admin.affiliates.sent', $entry->id) }}"
                            >
                                {{ __('Set as Sent') }}
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            class="text-center"
                            colspan="6"
                        >
                            {{ __('There is no succesfull withdrawal request') }}
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-table>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/affiliate.js') }}"></script>
@endpush
