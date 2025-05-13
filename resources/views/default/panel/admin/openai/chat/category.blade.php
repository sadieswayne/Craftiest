@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Chat Categories'))
@section('titlebar_subtitle', __('Manage Chat Categories'))
@section('titlebar_actions')
    <x-button
        onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
        href="{{ $app_is_demo ? '#' :  (route('dashboard.admin.openai.chat.addOrUpdateCategory')) }}"
    >
        {{ __('Add Category') }}
    </x-button>
@endsection

@section('content')
    <div class="py-6">
        <x-table class="table">
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Name') }}
                    </th>
                    <th>
                        {{ __('User') }}
                    </th>
                    <th class="text-end">
                        {{ __('Action') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach ($list as $entry)
                    <tr>
                        <td>
                            {{ $entry->name }}
                        </td>
                        <td>
                            {{ $entry->user?->name ?: trans('System') }}
                        </td>
                        <td class="text-end">
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
                                    href="{{  (route('dashboard.admin.openai.chat.addOrUpdateCategory', $entry->id)) }}"
                                    title="{{ __('Edit') }}"
                                >
                                    <x-tabler-pencil class="size-4" />
                                </x-button>
                                <x-button
                                    class="size-9"
                                    variant="ghost-shadow"
                                    hover-variant="danger"
                                    size="none"
                                    href="{{  (route('dashboard.admin.openai.chat.deleteCategory', $entry->id)) }}"
                                    onclick="return confirm('{{ __('Are you sure? This is permanent.') }}')"
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
