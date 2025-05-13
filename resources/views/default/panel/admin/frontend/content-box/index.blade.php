@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Content Box Section'))
@section('titlebar_actions')
@endsection
@section('content')
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Emoji') }}
                    </th>
                    <th>
                        {{ __('Title') }}
                    </th>
                    <th>
                        {{ __('Description') }}
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
                            {!! $item->emoji !!}
                        </td>
                        <td>
                            {{ $item->title }}
                        </td>
                        <td>
                            {{ $item->description }}
                        </td>
                        <td>
                            <p class="m-0">
                                {{ date('j.n.Y', strtotime($item->created_at)) }}
                                <span class="block opacity-60">
                                    {{ date('H:i:s', strtotime($item->created_at)) }}
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
                                    href="{{ route('dashboard.admin.frontend.content-box.edit', $item->id) }}"
                                    title="{{ __('Edit') }}"
                                >
                                    <x-tabler-pencil class="size-4" />
                                </x-button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-table>
    </div>
@endsection
@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/faq.js') }}"></script>
@endpush