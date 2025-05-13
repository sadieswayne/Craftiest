<div>
    <div class="flex justify-between">
        <label class="lqd-input-label flex cursor-pointer items-center gap-2 text-2xs font-medium leading-none text-label">
            <span class="lqd-input-label-txt">
                {{ __('Allowed Domains') }}
            </span>
        </label>
        @if ($addDomainButton)
            <button type="button" wire:click="showModal" class="mr-1 font-medium text-2xs text-primary">{{ __('Add domain') }}</button>
        @endif
    </div>
    <dd class="mt-2">
        @if ($this->domains->isNotEmpty())
            <ul role="list" class="divide-y divide-gray-100 rounded-md border border-input-border dark:divide-input-border">
                @foreach ($this->domains as $key => $domain)
                    <livewire:chatbot-domain :domain="$domain" wire:key="domain-{{ $domain->uuid }}" />
                @endforeach
            </ul>
        @else
            <x-alert variant="info" class="mt-1 w-full py-2.5">
                <p>
                    {{ __('No domains yet.') }}
                </p>
            </x-alert>
        @endif
    </dd>
    @if ($addDomainDialog)
        <x-modal-new
                 wire:model.live="addDomainDialog"
                 :title="__('Add Domain')"
{{--                 :description="$domain?->chatbot->title"--}}
                 focusable
                 max-width="sm"
                 :cancel-attributes="[
                     'wire:click' => 'closeModal',
                     'wire:loading.attr' => 'disabled',
                 ]">
            <div x-data="{}"
                           x-on:confirming-add-domain.window="setTimeout(() => $refs.domain.focus(), 250)">
                <input type="text" class="form-control w-full rounded-md border border-gray-200"
                              required
                              placeholder="{{ __('example.com') }}"
                              x-ref="domain"
                              autofocus
                              wire:model="domain"
                              wire:keydown.prevent.enter="addDomain" />

                @error('domain')
                    <p class="text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <x-slot:buttons>
                <div class="justify-self-end">
                    <x-button class="py-2 px-4" type="button" color="success" size="sm" wire:click.prevent="addDomain" wire:loading.attr="disabled">
                        {{ __('Add Domain') }}
                    </x-button>
                </div>
            </x-slot:buttons>
        </x-modal-new>
    @endif
</div>
