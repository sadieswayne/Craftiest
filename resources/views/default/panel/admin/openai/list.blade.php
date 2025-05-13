@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Built-in Templates'))
@section('titlebar_subtitle', __('Manage Built-in Prompts and Templates'))
@section('titlebar_actions', '')
@section('content')
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Template Name') }}
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
                            {{ __($entry->title) }}
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

                        <td>
                            @if ($app_is_demo)
                                @if (strpos($entry->slug, 'ai_') !== 0)
                                    <x-button
                                        class="size-9"
                                        variant="ghost-shadow"
                                        size="none"
                                        onclick="return toastr.info('This feature is disabled in Demo version.')"
                                        title="{{ __('Edit') }}"
                                    >
                                        <x-tabler-pencil class="size-4" />
                                    </x-button>
                                @endif
                            @else
                                @if (strpos($entry->slug, 'ai_') !== 0)
                                    <x-button
                                        class="py-2 group-[&.active]:flex"
                                        variant="ghost-shadow"
                                        size="none"
                                        href="{{  (route('dashboard.admin.openai.custom.addOrUpdate', $entry->id)) }}"
                                        title="{{ __('Edit') }}"
                                    >
                                        <x-tabler-pencil class="size-4" />
                                    </x-button>
                                @endif
                            @endif
                            <div>
                                <x-button
                                    class="hidden group-[&.active]:flex"
                                    id="active_btn_{{ $entry->id }}"
                                    variant="success"
                                    onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\');' : 'return updateStatus(0, ' . $entry->id . ');' }}"
                                >
                                    {{ __('Active') }}
                                </x-button>
                                <x-button
                                    class="hidden group-[&.passive]:flex"
                                    id="passive_btn_{{ $entry->id }}"
                                    variant="danger"
                                    onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\');' : 'return updateStatus(1, ' . $entry->id . ');' }}"
                                >
                                    {{ __('Passive') }}
                                </x-button>
                            </div>
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
