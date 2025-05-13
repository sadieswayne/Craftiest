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
    'type' => 'text',
])
<div x-data="{
               type: 'password',
               get inputValueVisible() { return this.type !== 'password' },
               toggleType() {
                 this.type = this.type === 'text' ? 'password' : 'text';
               }
             }">

    <x-form.wrapper :force="true">

        <input {{ $attributes->class(['form-control']) }}
               :type="type"
               :id="$id('text-input')"
               type="password"
               @if($error)
                   aria-invalid="true" autofocus x-bind:aria-describedby="@if ($id ?? '') {{ $id }}-error @else $id('text-input') + '-error' @endif"
                @endif
        />

        <button
                class="lqd-show-password size-7 absolute end-3 top-1/2 z-10 inline-flex -translate-y-1/2 cursor-pointer items-center justify-center rounded bg-none transition-colors hover:bg-foreground/10"
                type="button"
                @click="toggleType()"
        >
            <x-tabler-eye class="w-5"
                          stroke-width="1.5"
                          ::class="inputValueVisible ? 'hidden' : ''" />

            <x-tabler-eye-off class="hidden w-5"
                              stroke-width="1.5"
                              ::class="inputValueVisible ? '!block' : 'hidden'" />
        </button>
    </x-form.wrapper>
</div>