<div
    @if ($stepper) x-data='{
		value: {{ !empty($value) ? $value : 0 }},
		min: {{ $attributes->has('min') ? $attributes->get('min') : 0 }},
		max: {{ $attributes->has('max') ? $attributes->get('max') : 999999 }},
		step: {{ $attributes->has('step') ? $attributes->get('step') : 1 }}
	}' @endif
    {{ $attributes->twMerge('form-group lqd-input-container relative') }}
    x-id="['text-input', 'input-description', 'input-error']"
>

    @if ($label && !$noGroupLabel)
        <x-form.label {{ $attributes->twMergeFor('label') }} />
    @endif

    {{ $slot }}

    @if ($error || $help)
        <div class="mb-0.5 mt-0.5 grid gap-y-0.5">
            @if ($error)
                <p
                    class="text-2xs text-red-500"
                    x-bind:id="$id('input-error')"
                >
                    {{ $error }}
                </p>
            @endif

            @if ($help)
                <p
                    class="text-2xs text-gray-500 dark:text-slate-400"
                    x-bind:id="$id('input-description')"
                >{{ $help }}</p>
            @endif
        </div>
    @endif
</div>
