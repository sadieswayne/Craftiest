<div class="space-y-8">
    @foreach ($this->plan->openAIGeneratorsValues() as $step => $items)
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
            <x-form-step
                class="col-span-2 m-0"
                step="{{ ++$loop->index }}"
                label="{{ __(ucfirst($step)) }}"
            />

            @foreach ($items as $generator)
                <x-form.group
                    class="col-span-2 sm:col-span-1"
                    no-group-label
                    :error="'plan.open_ai_items.' . $generator['slug']"
                >
                    <x-form.checkbox
                        class="border-input rounded-input border !px-2.5 !py-3"
                        wire:model="plan.open_ai_items.{{ $generator['slug'] }}"
                        value="{{ $generator['slug'] }}"
                        label="{{ $generator['title'] }}"
                        tooltip="{{ $generator['title'] }}"
                        switcher
                    />
                </x-form.group>
            @endforeach
        </div>
    @endforeach
</div>
