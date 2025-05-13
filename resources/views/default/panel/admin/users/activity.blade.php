@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('User Activity'))

@section('content')
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        <button
                                class="table-sort"
                                data-sort="sort-name"
                        >
                            {{ __('Email') }}
                        </button>
                    </th>
                    <th>
                        <button
                                class="table-sort"
                                data-sort="sort-group"
                        >
                            {{ __('User Type') }}
                        </button>
                    </th>
                    <th>
                        <button
                                class="table-sort"
                                data-sort="sort-remaining-words"
                        >
                            {{ __('IP Address') }}
                        </button>
                    </th>
                    <th>
                        <button
                                class="table-sort"
                                data-sort="sort-remaining-images"
                        >
                            {{ __('Connection') }}
                        </button>
                    </th>
                    <th>
                        <button
                                class="table-sort"
                                data-sort="sort-country"
                        >
                            {{ __('Last Activity') }}
                        </button>
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body
                    class="text-xs"
                    id="users-list"
            >
                @if ($app_is_not_demo)
                    @forelse ($users as $user)
                        <tr>
                            <td class="sort-name">
                                {{ $user->email }}
                            </td>
                            <td class="sort-group">
                                {{ $user->type }}
                            </td>
                            <td class="sort-remaining-words">
                                {{ $user->ip }}
                            </td>
                            <td class="sort-remaining-images">
                                {{ $user->connection }}
                            </td>
                            <td
                                    class="sort-date"
                                    data-date="{{ strtotime($user->created_at) }}"
                            >
                                <p class="m-0">
                                    {{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}
                                </p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                    class="text-center"
                                    colspan="8"
                            >
                                {{ __('No users found.') }}
                            </td>
                        </tr>
                    @endforelse
                @else
                    <tr>
                        <td class="sort-name">
                            admin@admin.com
                        </td>
                        <td class="sort-group">
                            Admin
                        </td>
                        <td class="sort-remaining-words">
                            192.168.2.1
                        </td>
                        <td class="sort-remaining-images">
                            Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko)
                            Chrome/124.0.0.0 Safari/537.36
                        </td>
                        <td
                                class="sort-date"
                                data-date="19-12-2022"
                        >
                            <p class="m-0">
                                19-12-2022
                            </p>
                            <p class="opacity-50">
                                19-12-2022
                            </p>
                        </td>
                    </tr>
                @endif
            </x-slot:body>
        </x-table>


        @if ($app_is_not_demo)
            <div class="mt-1 flex items-center justify-end border-t pt-4">
                <div class="m-0 ms-auto p-0">{{ $users->links() }}</div>
            </div>
        @endif
    </div>

@endsection
