<div class="mt-9 flex w-full flex-col gap-4">
    <x-button
        class="min-h-11 w-full"
        type="button"
        wire:click="nextStep"
        wire:offline.attr="disabled"
        wire:loading.attr="disabled"
        wire:target="nextStep"
        variant="secondary"
    >
        {{ $this->hasNextStep() ? __('Next') : __('Save') }}
        <span class="size-7 inline-grid place-content-center rounded-full bg-background dark:bg-heading-foreground dark:text-header-background">
            <x-tabler-chevron-right class="size-4" />
        </span>
    </x-button>

    <x-button
        class="min-h-11 w-full"
        type="button"
        wire:click="toPreviousStep"
        wire:offline.attr="disabled"
        wire:loading.attr="disabled"
        wire:target="toPreviousStep"
        :disabled="$this->currentStepIs(1)"
        variant="outline"
    >
        @lang('Back')
    </x-button>
</div>
