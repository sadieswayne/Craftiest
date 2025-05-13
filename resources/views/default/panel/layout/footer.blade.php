<footer class="lqd-page-footer mt-auto py-8">
    <div class="container">
        <div class="flex flex-wrap items-center gap-4 md:flex-nowrap">
            <div class="order-2 grow basis-full md:order-first md:basis-0 lg:ms-auto">
                <p>{{ __('Version') }}: {{ format_double($setting->script_version) }}</p>
                @if (Config::get('app.show_load_time') === true)
					{{ __('Load time') }}:
                    {{ microtime(true) - LARAVEL_START }}
                @endif
            </div>
            <div class="grow basis-full md:basis-0 md:text-end">
                <p>
                    {{ __('Copyright') }} &copy; <?php echo date('Y'); ?>
                    <a href="{{ route('index') }}">
                        {{ $setting->site_name }}
                    </a>.
                    {{ __('All rights reserved.') }}
                </p>
            </div>
        </div>
    </div>
</footer>
