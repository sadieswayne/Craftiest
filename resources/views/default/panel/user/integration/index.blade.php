@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Integrations'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('Send blog posts directly to your CMS'))

@section('content')
    <div class="py-10">
        <div class="lqd-extension-grid grid grid-cols-1 gap-7 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($items as $item)
                <x-card
                    class="text-center"
                    size="lg"
                >
                    <img
                        class="max-w-36 mx-auto mb-4 block"
                        src="{{ asset($item->image) }}"
                        alt="{{ $item->app }}"
                    />
                    <h3 class="mb-3">
                        {{ $item->app }}
                    </h3>
                    <p class='mb-4'>
                        {{ $item->description }}
                    </p>
                    @if ($item->hasExtension)
                        <x-button
                            href="{{ $app_is_demo ? '#' : route('dashboard.user.integration.edit', $item->id) }}"
                            onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}"
                            class="w-full"
                        >
                            @lang('Integrate')
                        </x-button>
                    @else
                        <p class="font-medium text-orange-600 dark:text-orange-500/80">
                            @lang('No installed extension')
                        </p>
                    @endif
                </x-card>
            @endforeach
        </div>
    </div>
@endsection
