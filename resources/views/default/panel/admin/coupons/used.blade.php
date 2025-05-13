@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', ($coupon?->name ? __($coupon?->name) . ' ' : '') . __('Coupon Users'))
@section('titlebar_actions', '')

@section('content')
    <!-- Page body -->
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('User') }}
                    </th>
                    <th>
                        {{ __('Email') }}
                    </th>
                    <th>
                        {{ __('Using Date') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @forelse ($coupon?->usersUsed ?? [] as $user)
                    <tr>
                        <td>
                            {{ $user->name . ' ' . $user->surname }}
                        </td>
                        <td>
                            {{ $user->email }}
                        </td>
                        <td>
                            {{ $user->pivot->created_at }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            class="text-center"
                            colspan="3"
                        >
                            {{ __('There is no users used the coupon yet') }}
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-table>
    </div>
@endsection
