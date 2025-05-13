@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Email Templates'))
@section('titlebar_actions')
    @if(\Illuminate\Support\Facades\View::exists('news-letter::add-new-button'))
        @include('news-letter::add-new-button')
    @elseif($installedExtension)
        <x-button
            href="{{ $app_is_demo ? '#' :  (route('dashboard.newsletter.create')) }}"
            onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
            variant="primary"
        >
            <x-tabler-plus class="size-4" />
            {{ __('Add New Template') }}
        </x-button>
    @endif
@endsection

@section('content')
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Title') }}
                    </th>
                    <th>
                        {{ __('Subject') }}
                    </th>
                    <th class="text-end">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach ($list as $entry)
                    <tr>
                        <td>
                            {{ $entry->title }}
                        </td>
                        <td>
                            {{ $entry->subject }}
                        </td>
                        <td class="whitespace-nowrap text-end">
                            @if (!$entry->system)
                                <x-button
                                    class="size-9"
                                    href="{{ route('dashboard.email-templates.send', $entry->id) }}"
                                    title="{{ __('Send') }}"
                                    variant="success"
                                    size="none"
                                >
                                    <x-tabler-send class="size-4" />
                                </x-button>
                            @endif
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
                                @if ($entry->role != 'default')
                                    <x-button
                                        class="size-9"
                                        variant="ghost-shadow"
                                        size="none"
                                        href="{{  (route('dashboard.email-templates.edit', $entry->id)) }}"
                                        title="{{ __('Edit') }}"
                                    >
                                        <x-tabler-pencil class="size-4" />
                                    </x-button>
                                @endif
                            @endif

                            @if (!$entry->system)
                                <x-button
                                    class="size-9"
                                    variant="ghost-shadow"
                                    hover-variant="danger"
                                    size="none"
                                    href="{{ route('dashboard.email-templates.destroy', $entry->id) }}"
                                    onclick="return confirm('Are you sure? This is permanent and will delete all documents related to email template.')"
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

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/openai.js') }}"></script>
@endpush
