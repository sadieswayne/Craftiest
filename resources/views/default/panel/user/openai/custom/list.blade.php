@extends('panel.layout.app')
@section('title', __('My Templates'))
@section('titlebar_subtitle', __('Manage Your Templates'))
@section('titlebar_actions')
    @if (setting('user_ai_writer_custom_templates', 0))
        <x-button href="{{ route('dashboard.user.openai.custom.addOrUpdate') }}">
            <x-tabler-plus class="size-4" />
            {{ __('Create Template') }}
        </x-button>
    @endif
@endsection
@section('content')
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Status') }}
                    </th>
                    <th>
                        {{ __('Template Name') }}
                    </th>
                    <th>
                        {{ __('User') }}
                    </th>
                    <th>
                        {{ __('Template Description') }}
                    </th>
                    <th>
                        {{ __('Package') }}
                    </th>
                    <th>
                        {{ __('Updated At') }}
                    </th>
                    <th>
                        {{ __('Actions') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach ($list as $entry)
                    <tr
                        id="template-{{ $entry->id }}"
                        @class([
                            'group',
                            'active' => $entry->active == 1,
                            'passive' => $entry->active == 0,
                        ])
                    >
                        <td>
                            <x-badge
                                class="hidden text-3xs group-[&.active]:block"
                                variant="success"
                            >
                                {{ __('Active') }}
                            </x-badge>
                            <x-badge
                                class="hidden text-3xs group-[&.passive]:block"
                                variant="danger"
                            >
                                {{ __('Passive') }}
                            </x-badge>
                        </td>
                        <td>
                            {{ __($entry->title) }}
                        </td>
                        <td>
                            {{ $entry?->user?->name ?: __('System') }}
                        </td>
                        <td>
                            {{ __($entry->description) }}
                        </td>
                        <td>
                            <x-forms.input
                                class="min-w-[110px]"
                                id="premium"
                                name="premium"
                                type="select"
                                size="lg"
                                :disabled="$app_is_demo"
                                onchange="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\');' : 'return updatePackageStatus(this.value, ' . $entry->id . ');' }}"
                            >
                                <option
                                    value="0"
                                    @selected($entry->premium == 0)
                                >
                                    {{ __('Regular') }}
                                </option>
                                <option
                                    value="1"
                                    @selected($entry->premium == 1)
                                >
                                    {{ __('Premium') }}
                                </option>
                            </x-forms.input>
                        </td>
                        <td>
                            <p class="m-0">
                                {{ date('j.n.Y', strtotime($entry->updated_at)) }}
                                <span class="block opacity-60">
                                    {{ date('H:i:s', strtotime($entry->updated_at)) }}
                                </span>
                            </p>
                        </td>
                        <td class="whitespace-nowrap">
                            <x-button
                                class="size-9"
                                size="none"
                                variant="ghost-shadow"
                                hover-variant="primary"
                                href="{{ route('dashboard.user.openai.custom.addOrUpdate', $entry->id) }}"
                                title="{{ __('Edit') }}"
                            >
                                <x-tabler-pencil class="size-4" />
                            </x-button>
                            <x-button
                                class="size-9"
                                size="none"
                                variant="ghost-shadow"
                                hover-variant="danger"
                                href="{{ route('dashboard.user.openai.custom.delete', $entry->id) }}"
                                onclick="return confirm('{{ __('Are you sure? This is permanent.') }}')"
                                title="{{ __('Delete') }}"
                            >
                                <x-tabler-x class="size-4" />
                            </x-button>
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
