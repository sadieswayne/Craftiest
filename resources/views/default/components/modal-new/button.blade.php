<button @click="open = true" type="button" {{ $attributes->merge() }}>
    {{ $slot ?? '' }}
</button>
