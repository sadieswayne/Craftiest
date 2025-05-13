@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Google Adsense'))
@section('titlebar_actions', '')
@section('titlebar_title')
    {{ __('Google Adsense List') }}
    <x-info-tooltip text="{{ __('Activate header section to view ads') }}" />
@endsection
@section('content')
    <div class="py-10">
        <x-table
            class='table'
            id='adsTable'
            width='100%'
        >
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Adsense Type') }}
                    </th>
                    <th>
                        {{ __('Status') }}
                    </th>
                    <th>
                        {{ __('Updated On') }}
                    </th>
                    <th class="text-end">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($data as $item)
                    <tr>
                        <td>
                            {{ $item->type }}
                        </td>
                        <td>
                            @if ($item->status)
                                <x-badge
                                    class="text-3xs group-[&.active]:block"
                                    variant="primary"
                                >
                                    {{ __('Active') }}
                                </x-badge>
                            @else
                                <x-badge
                                    class="text-3xs group-[&.passive]:block"
                                    variant="danger"
                                >
                                    {{ __('Passive') }}
                                </x-badge>
                            @endif
                        </td>
                        <td>
                            {{ date_format($item['updated_at'], 'd M Y H:i A') }}
                        </td>
                        <td class="text-end">
                            <x-button
                                class="size-9"
                                size="none"
                                variant="ghost-shadow"
                                href="{{ route('dashboard.admin.ads.edit', $item->id) }}"
                                title="{{ __('Edit') }}"
                            >
                                <x-tabler-pencil
                                    class="size-5"
                                    stroke-width="1.5"
                                />
                                <span class="sr-only">{{ __('Edit') }}</span>
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            {{ __('No ads created yet') }}
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-table>

        @if ($app_is_not_demo)
            <div class="flex items-center border-t pb-6 pt-10">
                <div class="m-0 ms-auto p-0">{{ $data->links() }}</div>
            </div>
        @endif
    </div>
@endsection
