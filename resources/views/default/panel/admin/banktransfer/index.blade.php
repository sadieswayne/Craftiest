@extends('panel.layout.app')
@section('title', __('Bank Transactions'))

@section('content')

    @php
        $currencySymbol = currency()->symbol;
    @endphp
    <div class="py-10">
        <x-table class="table-vcenter table">
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Order ID') }}
                    </th>
                    <th>
                        {{ __('Proof Of Purchase') }}
                    </th>
                    <th>
                        {{ __('Status') }}
                    </th>
                    <th>
                        {{ __('Date') }}
                    </th>
                    <th>
                        {{ __('Info') }}
                    </th>
                    <th colspan="3">
                        {{ __('Plan') }} }}
                    </th>
                    <th class="text-end">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach ($bankOrders as $order)
                    <tr>
                        <td>
                            {{ __($order->order_id) }}
                            <br>
                            <span class="opacity-70">
                                {{ $order->created_at }}
                            </span>
                        </td>
                        <td>
                            <a
                                href="{{ getImageUrlByOrderId($order->order_id) }}"
                                download
                            >
                                <img
                                    src="{{ getImageUrlByOrderId($order->order_id) }}"
                                    alt="Proof of Purchase"
                                    style="max-width: 100px;"
                                >
                            </a>
                        </td>
                        @php
                            $badge_type = 'primary';

                            switch ($order->status) {
                                case 'Waiting':
                                    $badge_type = 'secondary';
                                    break;
                                case 'Approved':
                                    $badge_type = 'success';
                                    break;
                                case 'Rejected':
                                    $badge_type = 'warning';
                                    break;
                                default:
                                    $badge_type = 'danger';
                                    break;
                            }
                        @endphp
                        <td>
                            <x-badge
                                class="text-3xs"
                                variant="{{ $badge_type }}"
                            >
                                {{ __($order->status) }}
                            </x-badge>
                        </td>
                        <td>
                            <p class="m-0">
                                {{ date('j.n.Y', strtotime($order->created_at)) }}
                                <span class="block opacity-60">
                                    {{ date('H:i:s', strtotime($order->created_at)) }}
                                </span>
                            </p>
                        </td>
                        <td class="text-foreground/60">
                            <span class="text-heading-foreground">
                                {{ $order->user?->fullName() }}
                            </span>
                            <br>
                            {{ __($order->type) }} /
                            {{ $order->type == 'subscription' ? __(formatCamelCase(@$order->plan->frequency)) : __('One Time') }}</span>
                            <br>
                            {{ __('Total') }} :
                            @if (currencyShouldDisplayOnRight($currencySymbol))
                                {{ $order->price }}{{ $currencySymbol }}
                            @else
                                {{ $currencySymbol }}{{ $order->price }}
                            @endif
                        </td>
                        <td
                            class="w-1"
                            colspan="3"
                        >
                            <span class="font-medium text-primary">
                                {{ @$order->plan->name ?? __('Archived Plan') }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap text-end">
                            <x-modal
                                class:modal-backdrop="backdrop-blur-none bg-foreground/15"
                                class="inline-flex"
                                title="{{ __('Order ID') }}: {{ $order->order_id }}"
                            >
                                <x-slot:trigger
                                    class="size-9"
                                    variant="ghost-shadow"
                                    size="none"
                                    title="{{ __('View') }}"
                                >
                                    <x-tabler-eye class="size-4" />
                                </x-slot:trigger>

                                <x-slot:modal>
                                    <div class="flex flex-col gap-1">
                                        @if ($order->user)
                                            <p>
                                                <strong>
                                                    {{ __('User:') }}
                                                </strong>
                                                {{ $order->user->name }}
                                            </p>
                                            <p>
                                                <strong>
                                                    {{ __('Email:') }}
                                                </strong>
                                                {{ $order->user->email }}
                                            </p>
                                        @endif
                                        @if ($order->plan)
                                            <p>
                                                <strong>
                                                    {{ __('Plan name:') }}
                                                </strong>
                                                {{ $order->plan->name }}
                                            </p>
                                            <p>
                                                <strong>
                                                    {{ __('Plan price:') }}
                                                </strong>
                                                {{ $currencySymbol }}{{ $order->plan->price }}
                                            </p>
                                        @endif
                                        <p>
                                            <strong>
                                                {{ __('Tax Rate:') }}
                                            </strong>
                                            {{ $order->tax_rate }}%
                                        </p>
                                        <p>
                                            <strong>
                                                {{ __('Tax:') }}
                                            </strong>
                                            {{ $currencySymbol }}{{ $order->tax_value }}
                                        </p>
                                        <p>
                                            <strong>
                                                {{ __('Total:') }}
                                            </strong>
                                            {{ $currencySymbol }}{{ $order->price }}
                                        </p>
                                    </div>
                                </x-slot:modal>
                            </x-modal>
                            @if ($app_is_demo)
                                <x-button
                                    class="size-9"
                                    variant="ghost-shadow"
                                    size="none"
                                    onclick="return toastr.info('This feature is disabled in Demo version.')"
                                    title="{{ __('Edit') }}"
                                >
                                    <x-tabler-pencil class="size-4" />
                                </x-button>
                            @else
                                @if ($order->role != 'default')
                                    <x-modal
                                        class:modal-backdrop="backdrop-blur-none bg-foreground/15"
                                        class="inline-flex"
                                        title="{{ __('Change Order Status') }}: {{ $order->order_id }}"
                                    >
                                        <x-slot:trigger
                                            class="size-9"
                                            variant="ghost-shadow"
                                            size="none"
                                            title="{{ __('Edit') }}"
                                        >
                                            <x-tabler-pencil class="size-4" />
                                        </x-slot:trigger>

                                        <x-slot:modal>
                                            <form
                                                action="{{ route('dashboard.admin.bank.transactions.update') }}"
                                                method="POST"
                                            >
                                                @csrf
                                                <x-forms.input
                                                    type="select"
                                                    size="lg"
                                                    label="{{ __('Current Status') }}"
                                                    name="order_status"
                                                >
                                                    <option
                                                        value="0"
                                                        disabled
                                                        selected
                                                    >
                                                        {{ __('Select Status') }}
                                                    </option>
                                                    <option
                                                        value="Waiting"
                                                        @selected($order->status === 'Waiting')
                                                    >
                                                        {{ __('Waiting') }}
                                                    </option>
                                                    <option
                                                        value="Approved"
                                                        @selected($order->status === 'Approved')
                                                    >
                                                        {{ __('Approved') }}
                                                    </option>
                                                    <option
                                                        value="Rejected"
                                                        @selected($order->status === 'Rejected')
                                                    >
                                                        {{ __('Rejected') }}
                                                    </option>
                                                </x-forms.input>
                                                <input
                                                    id="orderIdInput"
                                                    type="hidden"
                                                    name="order_id"
                                                    value="{{ $order->id }}"
                                                >
                                                <div class="mt-4 border-t pt-3">
                                                    <x-button
                                                        @click.prevent="modalOpen = false"
                                                        variant="outline"
                                                    >
                                                        {{ __('Cancel') }}
                                                    </x-button>
                                                    <x-button
                                                        tag="button"
                                                        variant="primary"
                                                        type="submit"
                                                    >
                                                        {{ __('Save Changes') }}
                                                    </x-button>
                                                </div>
                                            </form>
                                        </x-slot:modal>
                                    </x-modal>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-table>
    </div>
@endsection
