@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', isset($user) ? $user?->fullName() . " " . ('Orders') :  __('My Orders'))
@section('titlebar_actions')
	<x-dropdown.dropdown
		class="header-language-dropdown"
		anchor="end"
		offsetY="20px"
	>
		<x-slot:trigger
			class="w-full justify-start rounded-none px-3 py-2 text-2xs hover:translate-y-0 hover:bg-foreground/5 hover:shadow-none focus-visible:bg-foreground/5"
		>
			{{ __('Export All') }}
			<x-tabler-dots-vertical class="size-5 ml-2" />
		</x-slot:trigger>

		<x-slot:dropdown
			class="overflow-hidden whitespace-nowrap py-1 text-2xs font-medium group-[&[data-view-mode=grid]]:-me-3"
		>
			<x-button
				class="flex w-full justify-start rounded-none px-3 py-2 text-2xs shadow-none hover:translate-y-0 hover:bg-foreground/5 hover:text-inherit hover:shadow-none focus-visible:bg-foreground/5 focus-visible:text-inherit"
				size="none"
				variant="ghost-shadow"
				href="{{ route('dashboard.user.orders.ordersExport', ['type' => 'pdf']) }}"
			>
				<x-tabler-download class="size-4 text-blue-600" />
				{{ __('Pdf') }}
			</x-button>
		</x-slot:dropdown>
	</x-dropdown.dropdown>
@endsection
@section('content')
    <!-- Page body -->
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Order Id') }}
                    </th>
                    <th>
                        {{ __('Status') }}
                    </th>
                    <th>
                        {{ __('Plan Name') }}
                    </th>
                    <th>
                        {{ __('Price') }}
                    </th>
                    <th>
                        {{ __('Tax') }}
                    </th>
                    <th>
                        {{ __('Type') }}
                    </th>
                    <th>
                        {{ __('Date') }}
                    </th>
                    <th class="text-end">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach ($list as $entry)
                    <tr>
                        <td>
                            {{ $entry->order_id }}
                        </td>
                        @php
                            switch ($entry->status) {
                                case 'Success':
                                    $badge_type = 'success';
                                    break;
                                case 'Waiting':
                                    $badge_type = 'secondary';
                                    break;
                                case 'Approved':
                                    $badge_type = 'success';
                                    break;
                                case 'Rejected':
                                    $badge_type = 'danger';
                                    break;
                                default:
                                    $badge_type = 'default';
                                    break;
                            }
                        @endphp
                        <td>
                            <x-badge
                                class="text-[12px]"
                                variant="{{ $badge_type }}"
                            >
                                {{ __($entry->status) }}
                            </x-badge>
                        </td>
                        <td>
                            {{ __(@$entry->plan->name ?? 'Archived') }}
                        </td>
                        <td>
                            @if (currencyShouldDisplayOnRight(currency()->symbol))
                                {{ $entry->price }}{{ currency()->symbol }}
                            @else
                                {{ currency()->symbol }}{{ $entry->price }}
                            @endif
                        </td>
                        <td>
                            @if (currencyShouldDisplayOnRight(currency()->symbol))
                                {{ $entry->tax_value ?? 0 }}{{ currency()->symbol }}
                            @else
                                {{ currency()->symbol }}{{ $entry->tax_value ?? 0 }}
                            @endif
                        </td>
                        <td>
                            {{ __($entry->type) }}
                        </td>
                        <td>
                            <p class="m-0">
                                {{ date('j.n.Y', strtotime($entry->created_at)) }}
                                <span class="block opacity-60">
                                    {{ date('H:i:s', strtotime($entry->created_at)) }}
                                </span>
                            </p>
                        </td>
                        <td class="whitespace-nowrap text-end">
                            <x-button
                                class="size-9"
                                href="{{  (route('dashboard.user.orders.invoice', $entry->order_id)) }}"
                                size="none"
                                variant="ghost-shadow"
                                title="{{ __('Invoice') }}"
                            >
                                <x-tabler-news class="size-5" />
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-table>
    </div>
@endsection
