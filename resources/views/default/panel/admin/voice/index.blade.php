@extends('panel.layout.app', ['disable_tblr' => true])

@section('title', __('AI Voice Clone'))
@section('titlebar_actions')
    <x-button href="{{  (route('dashboard.user.voice.create')) }}">
        <x-tabler-plus class="size-4" />
        {{ __('Add New') }}
    </x-button>
@endsection
@section('content')
    <div class="py-10">
        <h2 class="mb-5">
            {{ __('Voice training') }}
        </h2>

        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Name') }}
                    </th>
                    <th>
                        {{ __('Voice id') }}
                    </th>
                    <th>
                        {{ __('Status') }}
                    </th>
                    <th>
                        {{ __('Created At') }}
                    </th>
                    <th class="text-end">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach ($items as $item)
                    <tr>
                        <td>
                            {{ $item->name }}
                        </td>
                        <td>
                            {{ $item->voice_id }}
                        </td>
                        <td>
                            <x-badge variant="{{ $item->status ? 'success' : 'danger' }}">
                                {{ $item->status ? __('Active') : __('Inactive') }}
                            </x-badge>
                        </td>
                        <td>
                            <p class="m-0">
                                {{ date('j.n.Y', strtotime($item->updated_at)) }}
                                <span class="block opacity-60">
                                    {{ date('H:i:s', strtotime($item->updated_at)) }}
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
                                    href="{{ route('dashboard.user.voice.edit', $item->id) }}"
                                    title="{{ __('Edit') }}"
                                >
                                    <x-tabler-pencil class="size-4" />
                                </x-button>
                                <x-button
                                    class="size-9"
                                    variant="ghost-shadow"
                                    hover-variant="danger"
                                    size="none"
                                    onclick="return confirm('{{ __('Are you sure? This is permanent and will delete all documents related to user.') }}')"
                                    href="{{ route('dashboard.user.voice.destroy', $item->id) }}"
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

        <div class="mt-5 flex items-center justify-end">
            {{ $items->links() }}
        </div>
    </div>
@endsection
