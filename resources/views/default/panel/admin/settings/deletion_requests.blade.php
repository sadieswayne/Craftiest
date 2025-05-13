@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('User Deletion Requests'))

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
                            {{ __('User Name') }}
                        </button>
                    </th>
                    <th>
                        <button
                            class="table-sort"
                            data-sort="sort-group"
                        >
                            {{ __('User Email') }}
                        </button>
                    </th>
                    {{-- accept action --}}
                    <th>
                        <button>
                            {{ __('Accept User Deletion') }}
                        </button>
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body
                class="text-xs"
                id="users-list"
            >
                @if ($app_is_not_demo)
                    @forelse ($deletionRequests as $deletionRequest)
                        <tr>
                            <td class="sort-name">
                                {{ $deletionRequest->user?->fullName() }}
                            </td>
                            <td class="sort-group">
                                {{ $deletionRequest->user->email }}
                            </td>
                            <td>
                                <x-button
                                    variant="danger"
                                    onclick="checkAndConfirm('{{ __('Please be aware that all data and information stored in this account will be permanently deleted and cannot be recovered.') }}', {{ $deletionRequest->user->id }})"
                                >
                                    {{ __('Accept') }}
                                </x-button>
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
                            Admin
                        </td>
                        <td class="sort-group">
                            admin@admin.com
                        </td>
                        <td>
                            <x-button
                                variant="danger"
                                onclick="return toastr.info('{{ __('Admin settings disabled on Demo version.') }}')"
                            >
                                {{ __('Accept') }}
                            </x-button>
                        </td>
                    </tr>
                @endif
            </x-slot:body>
        </x-table>
    </div>

@endsection

@push('script')
    <script>
        function checkAndConfirm(message, userid) {
            if (confirm(message)) {
                $.ajax({
                    url: '/dashboard/admin/users/deletion/requests/' + userid,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            }
        }
    </script>
@endpush
