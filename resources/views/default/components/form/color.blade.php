@aware(['icon', 'action', 'size', 'error'])
@props([
    'icon' => null,
    'action' => null,
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
])
<x-form.wrapper>
    <div class="form-control flex items-center gap-3 {{ $sizes[$size] }}"
         @if($attributes->has('wire:model'))
             x-data="{ 'colorVal': $wire.entangle('{{ $attributes->has('wire:model') }}') }"
         @else
             x-data="{ 'colorVal': '{{ $attributes->get('value', '#ffffff') }}' }"
            @endif
    >
        <div class="size-5 relative gap-4 overflow-hidden rounded-full border shadow-sm focus-within:ring focus-within:ring-secondary">
            <input {{ $attributes->class([
                    'relative -start-1/2 -top-1/2 h-[200%] w-[200%] cursor-pointer appearance-none rounded-full border-none p-0',
                ]) }}
                   :id="$id('text-input')"
                   :value="colorVal"
                   x-model.debounce.500ms="colorVal"
                   type="color"
                   @if($error)
                       aria-invalid="true" autofocus x-bind:aria-describedby="@if ($id ?? '') {{ $id }}-error @else $id('text-input') + '-error' @endif"
                    @endif
            />
        </div>
        <input class="border-none bg-transparent text-inherit outline-none"
               :id="$id('text-input') + '_value'"
               type="text"
               x-model.debounce.500ms="colorVal"
               :value="colorVal"
        />
    </div>
</x-form.wrapper>