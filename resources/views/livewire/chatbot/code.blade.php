<div>
    <button type="button" wire:click="showCodeModal" class="text-sm">
        <svg title="Show Code" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 flex-shrink-0 text-gray-400 hover:text-gray-500 dark:hover:text-gray-500">
            <path fill-rule="evenodd" d="M14.447 3.026a.75.75 0 0 1 .527.921l-4.5 16.5a.75.75 0 0 1-1.448-.394l4.5-16.5a.75.75 0 0 1 .921-.527ZM16.72 6.22a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06L21.44 12l-4.72-4.72a.75.75 0 0 1 0-1.06Zm-9.44 0a.75.75 0 0 1 0 1.06L2.56 12l4.72 4.72a.75.75 0 0 1-1.06 1.06L.97 12.53a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
        </svg>
    </button>


    @if ($showCodeDialog)
        <x-modal-new
                wire:model.live="showCodeDialog"
                :title="__('External Chat Code')"
                {{--                :description="$domain->chatbot->title"--}}
                focusable
                max-width="2xl"
                show-cancel-button
                :buttons="true"
                cancel-button-text="{{ __('Close') }}"
                :cancel-attributes="[
                         'wire:click' => 'closeCodeModal',
                         'wire:loading.attr' => 'disabled',
                     ]">

            <div x-data="{}" x-on:confirming-edit-domain.window="setTimeout(() => $refs.domain.focus(), 250)">
                <div class="grid gap-6">
                    <div class="grid gap-y-1">
                        <label class="form-label">Script Code</label>
                        <div x-data="clipboard({ text: '{{ $scriptCode }}', message: '{{ __('Script Code successfully copied!') }}' })" class="flex w-full gap-3">
                            <textarea class="w-full rounded-md border border-gray-200 form-control">{{ $scriptCode }}</textarea>
                            <x-button class="self-center px-5 py-2 min-w-max" type="button" variant="secondary" size="sm" x-bind="clipboard">
                                {{ __('Copy to Clipboard') }}
                            </x-button>
                        </div>
                    </div>
                    <div class="grid gap-y-1">
                        <label class="form-label">Embed Code</label>
                        <div x-data="clipboard({ text: '{{ $embedCode }}', message: '{{ __('Embed Code successfully copied!') }}' })" class="flex w-full gap-3">
                            <textarea class="w-full rounded-md border border-gray-200 form-control">{{ $embedCode }}</textarea>
                            <x-button class="self-center px-5 py-2 min-w-max" type="button" variant="secondary" size="sm" x-bind="clipboard">
                                {{ __('Copy to Clipboard') }}
                            </x-button>
                        </div>
                    </div>
                    <div class="grid gap-y-1">
                        <label class="form-label">APP Key <span class="text-xs font-light text-gray-500">for {{ $domain->domain }}</span></label>
                        <div x-data="clipboard({ text: '{{ $appKey }}', message: '{{ __('APP Key successfully copied!') }}' })" class="flex w-full gap-3">
                            <input class="w-full rounded-md border border-gray-200 form-control" value="{{ $appKey }}">
                            <x-button class="self-center px-5 py-2 min-w-max" type="button" variant="secondary" size="sm" x-bind="clipboard">
                                {{ __('Copy to Clipboard') }}
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>
        </x-modal-new>
    @endif
</div>

{{--<div x-data="clipboard({ text: '{{ $domain->app_key }}', message: '{{ __('APP Key successfully copied!') }}' })">--}}
{{--    <button type="button" class="text-2xs dark:text-gray-300" x-bind="clipboard">--}}


{{--        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 flex-shrink-0 text-gray-400 hover:text-gray-500 dark:hover:text-gray-500">--}}
{{--            <path fill-rule="evenodd" d="M17.663 3.118c.225.015.45.032.673.05C19.876 3.298 21 4.604 21 6.109v9.642a3 3 0 0 1-3 3V16.5c0-5.922-4.576-10.775-10.384-11.217.324-1.132 1.3-2.01 2.548-2.114.224-.019.448-.036.673-.051A3 3 0 0 1 13.5 1.5H15a3 3 0 0 1 2.663 1.618ZM12 4.5A1.5 1.5 0 0 1 13.5 3H15a1.5 1.5 0 0 1 1.5 1.5H12Z" clip-rule="evenodd" />--}}
{{--            <path d="M3 8.625c0-1.036.84-1.875 1.875-1.875h.375A3.75 3.75 0 0 1 9 10.5v1.875c0 1.036.84 1.875 1.875 1.875h1.875A3.75 3.75 0 0 1 16.5 18v2.625c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625v-12Z" />--}}
{{--            <path d="M10.5 10.5a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963 5.23 5.23 0 0 0-3.434-1.279h-1.875a.375.375 0 0 1-.375-.375V10.5Z" />--}}
{{--        </svg>--}}


{{--        code icon:--}}

{{--        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 flex-shrink-0 text-gray-400 hover:text-gray-500 dark:hover:text-gray-500">--}}
{{--            <path fill-rule="evenodd" d="M14.447 3.026a.75.75 0 0 1 .527.921l-4.5 16.5a.75.75 0 0 1-1.448-.394l4.5-16.5a.75.75 0 0 1 .921-.527ZM16.72 6.22a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06L21.44 12l-4.72-4.72a.75.75 0 0 1 0-1.06Zm-9.44 0a.75.75 0 0 1 0 1.06L2.56 12l4.72 4.72a.75.75 0 0 1-1.06 1.06L.97 12.53a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />--}}
{{--        </svg>--}}

{{--    </button>--}}
{{--</div>--}}