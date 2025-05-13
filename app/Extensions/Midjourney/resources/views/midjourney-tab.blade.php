<x-button
	class="lqd-image-generator-tabs-trigger py-2 text-2xs font-bold text-heading-foreground hover:shadow-none [&.active]:bg-foreground/10"
	data-generator-name="midjourney"
	tag="button"
	type="button"
	variant="ghost"
	x-data
	::class="{ 'active': activeGenerator === 'midjourney' }"
	x-bind:data-active="activeGenerator === 'midjourney'"
	@click="changeActiveGenerator('midjourney')"
>
	{{ trans('Midjourney') }}
</x-button>
