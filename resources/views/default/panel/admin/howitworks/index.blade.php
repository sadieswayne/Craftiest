@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('How it Works'))
@section('titlebar_actions')
    <x-button
        class="mb-4"
        variant="primary"
        onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
        href="{{ $app_is_demo ? '#' : route('dashboard.admin.howitWorks.HowitWorksNewOrEdit') }}"
    >
        <x-tabler-plus class="size-4" />
        {{ __('Create New Step') }}
    </x-button>
@endsection
@section('content')
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Order') }}
                    </th>
                    <th>
                        {{ __('Title') }}
                    </th>
                    <th>
                        {{ __('Description') }}
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
                @foreach ($howitWorksList as $entry)
                    <tr>
                        <td>
                            {{ $entry->order }}
                        </td>
                        <td>
                            {!! $entry->title !!}
                        </td>
                        <td>
                            {!! $entry->description !!}
                        </td>
                        <td>
                            <p class="m-0">
                                {{ date('j.n.Y', strtotime($entry->created_at)) }}
                                <span class="block opacity-60">
                                    {{ date('H:i:s', strtotime($entry->created_at)) }}
                                </span>
                            </p>
                        </td>
                        <td>
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
                                    href="{{ route('dashboard.admin.howitWorks.HowitWorksNewOrEdit', $entry->id) }}"
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
                                    href="{{ route('dashboard.admin.howitWorks.delete', $entry->id) }}"
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

        @if ($app_is_not_demo)
            <x-card class="mt-10">
                <h3 class="mb-4">
                    {{ __('Bottom Line Settings') }}
                </h3>
                <div
                    class="flex flex-col gap-4"
                    id="bottomlinesettings"
                >
                    <x-forms.input
                        id="bottomlinecheck"
                        type="checkbox"
                        :checked="$defaults['option'] == 1"
                        label="{{ __('Display Bottom Line') }}"
                        onchange="howitWorksUpdate(0);"
                        switcher
                    />

                    <div @class(['flex flex-col gap-3', 'hidden' => $defaults['option'] == 0])>
                        <x-forms.input
                            id="bottomlinetext"
                            name="bottomlinetext"
                            type="textarea"
                            label="{{ __('Bottom Line Text') }}"
                            placeholder="{{ __('Bottom line text. Accepts <a> tags for links') }}"
                        >{{ $defaults['html'] }}</x-forms.input>

                        <x-button
                            type="button"
                            onclick="howitWorksUpdate(1);"
                        >
                            {{ __('Save') }}
                        </x-button>

                        <p>
                            <span class="opacity-60">
                                {{ __('Preview') }} :
                            </span>
                            {!! $defaults['html'] !!}
                        </p>
                    </div>
                </div>
                <p class="mt-4">
                    <em>{{ __('*You can use HTML in Step Title and Bottom Line.') }}</em>
                </p>
            </x-card>
        @endif

    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/howitworks.js') }}"></script>
@endpush
