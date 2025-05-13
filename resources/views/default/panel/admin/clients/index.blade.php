@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Clients'))
@section('titlebar_actions')
    <x-button
        class="mb-4"
        variant="primary"
        onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
        href="{{ $app_is_demo ? '#' : route('dashboard.admin.clients.ClientsNewOrEdit') }}"
    >
        <x-tabler-plus class="size-4" />
        {{ __('Create New Client') }}
    </x-button>
@endsection
@section('content')
    <div class="py-10">
        <x-table class="table">
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Avatar') }}
                    </th>
                    <th>
                        {{ __('Alt') }}
                    </th>
                    <th>
                        {{ __('Title') }}
                    </th>
                    <th>
                        {{ __('Updated At') }}
                    </th>
                    <th class="text-end">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach ($clientList as $entry)
                    <tr>
                        <td>
                            <img
                                class="size-12 rounded-full object-cover object-center"
                                src="{{ url('') . isset($entry->avatar) ? (str_starts_with($entry->avatar, 'asset') ? custom_theme_url($entry->avatar) : '/clientAvatar/' . $entry->avatar) : custom_theme_url('/assets/img/auth/default-avatar.png') }}"
                                {{ $entry->alt }}
                            />
                        </td>
                        <td>
                            {{ $entry->alt }}
                        </td>
                        <td>
                            {{ $entry->title }}
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
                                <x-button
                                    class="size-9"
                                    variant="ghost-shadow"
                                    hover-variant="danger"
                                    size="none"
                                    onclick="return toastr.info('This feature is disabled in Demo version.')"
                                    title="{{ __('Delete') }}"
                                >
                                    <x-tabler-x class="size-4" />
                                </x-button>
                            @else
                                <x-button
                                    class="size-9"
                                    variant="ghost-shadow"
                                    size="none"
                                    href="{{ route('dashboard.admin.clients.ClientsNewOrEdit', $entry->id) }}"
                                    title="{{ __('Edit') }}"
                                >
                                    <x-tabler-pencil class="size-4" />
                                </x-button>
                                <x-button
                                    class="size-9"
                                    variant="ghost-shadow"
                                    hover-variant="danger"
                                    size="none"
                                    onclick="return confirm('Are you sure?');"
                                    href="{{ route('dashboard.admin.clients.delete', $entry->id) }}"
                                    title="{{ __('Delete') }}"
                                >
                                    <x-tabler-x class="size-4" />
                                </x-button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-table>
    </div>
@endsection
