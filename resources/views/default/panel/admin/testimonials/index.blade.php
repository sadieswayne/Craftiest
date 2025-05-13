@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Testimonials'))
@section('titlebar_actions')
    <x-button
        class="mb-4"
        variant="primary"
        onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
        href="{{ $app_is_demo ? '#' : route('dashboard.admin.testimonials.TestimonialsNewOrEdit') }}"
    >
        <x-tabler-plus class="size-4" />
        {{ __('Add New') }}
    </x-button>
@endsection
@section('content')
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('Avatar') }}
                    </th>
                    <th>
                        {{ __('Full Name') }}
                    </th>
                    <th>
                        {{ __('Job Title') }}
                    </th>
                    <th>
                        {{ __('Testimonial Text') }}
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
                @foreach ($testimonialList as $entry)
                    <tr>
                        <td>
                            <img
                                class="size-12 rounded-full object-cover object-center"
                                src="{{ url('') . isset($entry->avatar) ? (str_starts_with($entry->avatar, 'asset') ? custom_theme_url($entry->avatar) : '/testimonialAvatar/' . $entry->avatar) : custom_theme_url('/assets/img/auth/default-avatar.png') }}"
                                alt="{{ $entry->full_name }}"
                            />
                        </td>
                        <td>
                            {{ $entry->full_name }}
                        </td>
                        <td>
                            {{ $entry->job_title }}
                        </td>
                        <td>{!! $entry->words !!}</td>
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
                                    href="{{ route('dashboard.admin.testimonials.TestimonialsNewOrEdit', $entry->id) }}"
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
                                    href="{{ route('dashboard.admin.testimonials.delete', $entry->id) }}"
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
