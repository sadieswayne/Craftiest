@aware(['icon', 'action', 'size', 'error', 'stepper'])
@props([
    'icon' => null,
    'action' => null,
    'stepper' => null,
    'error' => null,
    'size' => 'md',
    'addNew' => false,
    'name' => null,
])

@if ($addNew)
    <div x-data="{ 'newOptions': [] }">
@endif

<select
    {{ $attributes->class(['form-select', 'form-input-stepper' => $stepper]) }}
    :id="$id('text-input')"
    @if ($error) aria-invalid="true" autofocus x-bind:aria-describedby="@if ($id ?? '') {{ $id }}-error @else $id('text-input') + '-error' @endif"
    @endif
    >
    {{ $slot ?? '' }}

    @if ($addNew)
        <template
            x-for="option in newOptions"
            :key="option"
        >
            <option
                x-text="option"
                x-bind:value="option"
            ></option>
        </template>
    @endif
</select>

@if ($attributes->has('multiple'))
    <small class="mt-1 block">
        {{ __('Hold cmd(on mac) or ctrl(on pc) to select multiple items.') }}
    </small>
@endif

@if ($addNew)
    @php
        $newId = str()->random(10);
    @endphp
    <x-modal
        class:modal-backdrop="backdrop-blur-sm bg-foreground/15"
        title="{{ __('New value') }}"
    >
        <x-slot:trigger
            class="mt-3"
            variant="primary"
        >
            <x-tabler-plus
                class="size-3"
                stroke-width="3"
            />
            {{ __('Add New') }}
        </x-slot:trigger>

        <x-slot:modal
            x-data="{}"
        >
            <x-form.text
                id="new_{{ $newId }}"
                @keyup.enter="$refs.submitBtn.click(); modalOpen = false"
                name="new_{{ $name }}"
                size="lg"
                x-ref="new_{{ $newId }}"
            />
            <div class="mt-4 border-t pt-3">
                <x-button
                    @click.prevent="modalOpen = false"
                    variant="outline"
                >
                    {{ __('Cancel') }}
                </x-button>
                <x-button
                    tag="button"
                    variant="primary"
                    x-ref="submitBtn"
                    @click="newOptions.push($refs.new_{{ $newId }}.value); $refs.new_{{ $newId }}.value = ''; modalOpen = false;"
                >
                    {{ __('Add') }}
                </x-button>
            </div>
        </x-slot:modal>
    </x-modal>
@endif
@if ($addNew)
    </div>
@endif
