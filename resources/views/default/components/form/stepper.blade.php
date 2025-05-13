@aware(['icon', 'action', 'size', 'error', 'stepper'])
@props([
    'icon' => null,
    'action' => null,
    'stepper' => true,
    'error' => null,
    'sizes' => [
        'none' => 'lqd-input-size-none rounded-lg',
        'sm' => 'lqd-input-sm h-9 rounded-md',
        'md' => 'lqd-input-md h-10 rounded-lg',
        'lg' => 'lqd-input-lg h-11 rounded-xl',
        'xl' => 'lqd-input-xl h-14 rounded-2xl px-6',
        '2xl' => 'lqd-input-2xl h-16 rounded-full px-8',
    ],
    'size' => 'lg',
    'type' => 'text',
    'alpineData' => [
        'value' => '%s',
        'min' => '%s',
        'max' => '%s',
        'step' => '%s',
    ],
    'series' => null,
])
<x-form.wrapper
    :attributes="$attributes"
    stepper
>

    <input
        {{ $attributes->class(['form-control text-center', 'form-input-stepper text-center flex justify-center items-center']) }}
        :id="$id('text-input')"
        type="{{ $type }}"
        x-data="{ value: @if ($attributes->has('wire:model')) @entangle($attributes->wire('model')) @else {{ $attributes->get('value') ?? 0 }} @endif }"
        x-model="value"
        x-on:input="value = (value).toString().includes('.') ? parseFloat(value).toFixed(2) : value"
        @if ($error) aria-invalid="true" autofocus x-bind:aria-describedby="@if ($id ?? '') {{ $id }}-error @else $id('text-input') + '-error' @endif"
        @endif/>

    <x-slot:inputActions>
        <button
            class="lqd-stepper-btn absolute start-0 top-0 inline-flex aspect-square h-full w-10 items-center justify-center rounded-s-input transition-colors hover:bg-heading-foreground hover:text-heading-background"
            type="button"
            @click="
                value = Math.max(min, Number(value) - step);
                @if ($series)
                    $wire.updateEntities('{{ $series }}', value)
                @endif
            "
        >
            <x-tabler-minus
                class="w-4"
                stroke-width="1.5"
            />
        </button>
        <button
            class="lqd-stepper-btn absolute end-0 top-0 inline-flex aspect-square h-full w-10 items-center justify-center rounded-e-input transition-colors hover:bg-heading-foreground hover:text-heading-background"
            type="button"
            @click="
                value = Math.min(max, Number(value) + step);
                @if ($series)
                    $wire.updateEntities('{{ $series }}', value)
                @endif
            "
        >
            <x-tabler-plus
                class="w-4"
                stroke-width="1.5"
            />
        </button>
    </x-slot:inputActions>
</x-form.wrapper>
