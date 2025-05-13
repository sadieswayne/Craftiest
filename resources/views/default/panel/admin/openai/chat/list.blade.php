@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Chat Templates'))
@section('titlebar_subtitle', __('Chat Templates'))
@section('titlebar_actions')
    <x-button
        class="mb-4"
        variant="primary"
        onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
        href="{{ $app_is_demo ? '#' :  (route('dashboard.admin.openai.chat.addOrUpdate')) }}"
    >
        {{ __('Add Template') }}
    </x-button>
@endsection
@section('content')
    <div class="py-10">
        <x-table class="table">
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Template Name') }}
                    </th>
                    <th>
                        {{ __('Template Description') }}
                    </th>
                    <th>
                        {{ __('User') }}
                    </th>
                    <th>
                        {{ __('Role') }}
                    </th>
                    <th>
                        {{ __('Plan') }}
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
                @foreach ($list as $entry)
                    <tr>
                        <td>
                            {{ $entry->name }}
                        </td>
                        <td>
                            {{ $entry->description }}
                        </td>
                        <td>
                            {{ $entry->user?->name ?: trans('System') }}
                        </td>
                        <td>
                            {{ $entry->role }}
                        </td>
                        <td>
                            <x-forms.input
                                class="min-w-24"
                                id="premium"
                                onchange="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : 'updateChatStatus(this.value, ' . $entry->id . ');' }}"
                                name="premium"
                                type="select"
                            >
                                <option
                                    value="premium"
                                    @selected($entry->plan == 'premium')
                                >
                                    {{ __('Premium') }}
                                </option>
                                <option
                                    value="regular"
                                    @selected($entry->plan == 'regular')
                                >
                                    {{ __('Regular') }}
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
                                    href="{{  (route('dashboard.admin.openai.chat.addOrUpdate', $entry->id)) }}"
                                    title="{{ __('Edit') }}"
                                >
                                    <x-tabler-pencil class="size-4" />
                                </x-button>
                                <x-button
                                    class="size-9"
                                    variant="ghost-shadow"
                                    hover-variant="danger"
                                    size="none"
                                    href="{{  (route('dashboard.admin.openai.chat.delete', $entry->id)) }}"
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

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/openai.js') }}"></script>
@endpush
