@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Blog Posts'))
@section('titlebar_actions')
    <x-button
        onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
        href="{{ $app_is_demo ? '' : route('dashboard.blog.addOrUpdate') }}"
    >
        <x-tabler-plus class="size-4" />
        {{ __('Add Post') }}
    </x-button>
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
                        {{ __('Category') }}
                    </th>
                    <th>
                        {{ __('Tag') }}
                    </th>
                    <th>
                        {{ __('Status') }}
                    </th>
                    <th>
                        {{ __('Author') }}
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
                            {{ $entry->title }}
                        </td>
                        <td>
                            @if ($entry->category)
                                @foreach (explode(',', $entry->category) as $cat)
                                    <a
                                        class="text-heading-foreground"
                                        target="_blank"
                                        href="{{ url('/blog/category', $cat) }}"
                                    >
                                        {{ $cat }}
                                    </a>
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if ($entry->tag)
                                @foreach (explode(',', $entry->tag) as $tag)
                                    <a
                                        class="font-normal text-heading-foreground"
                                        target="_blank"
                                        href="{{ url('/blog/tag', $tag) }}"
                                    >
                                        {{ $tag }}
                                    </a>
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <x-badge
                                class="text-2xs"
                                variant="{{ $entry->status == 1 ? 'success' : 'info' }}"
                            >
                                {{ $entry->status == 1 ? __('Published') : __('Draft') }}
                            </x-badge>
                        </td>
                        <td>
                            <a
                                class="text-heading-foreground"
                                target="_blank"
                                href="{{ url('/blog/author', $entry->user_id) }}"
                            >
                                {{ App\Models\User::where('id', $entry->user_id)->first()->name }}
                            </a>
                        </td>
                        <td>
                            <p class="m-0">
                                {{ date('j.n.Y', strtotime($entry->created_at)) }}
                                <span class="block opacity-60">
                                    {{ date('H:i:s', strtotime($entry->created_at)) }}
                                </span>
                            </p>
                        </td>
                        <td class="whitespace-nowrap text-end">
                            <x-button
                                class="size-9"
                                variant="ghost-shadow"
                                size="none"
                                href="{{ url('/blog', $entry->slug) }}"
                                title="{{ __('View') }}"
                                target="_blank"
                            >
                                <x-tabler-eye class="size-4" />
                            </x-button>
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
                                @if ($entry->role != 'default')
                                    <x-button
                                        class="size-9"
                                        variant="ghost-shadow"
                                        size="none"
                                        href="{{ route('dashboard.blog.addOrUpdate', $entry->id) }}"
                                        title="{{ __('Edit') }}"
                                    >
                                        <x-tabler-pencil class="size-4" />
                                    </x-button>
                                    <x-button
                                        class="size-9"
                                        variant="ghost-shadow"
                                        hover-variant="danger"
                                        size="none"
                                        href="{{ route('dashboard.blog.delete', $entry->id) }}"
                                        onclick="confirm('{{ __('Are you sure? This is permanent.') }}')"
                                        title="{{ __('Delete') }}"
                                    >
                                        <x-tabler-x class="size-4" />
                                    </x-button>
                                @endif
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
