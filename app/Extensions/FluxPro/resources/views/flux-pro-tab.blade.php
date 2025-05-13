<x-button
        class="lqd-image-generator-tabs-trigger py-2 text-2xs font-bold text-heading-foreground hover:shadow-none [&.active]:bg-foreground/10"
        data-generator-name="flux-pro"
        tag="button"
        type="button"
        variant="ghost"
        x-data
        ::class="{ 'active': activeGenerator === 'flux-pro' }"
        x-bind:data-active="activeGenerator === 'flux-pro'"
        @click="changeActiveGenerator('flux-pro')"
>
    @if(class_exists('\App\Domains\Entity\Enums\EntityEnum'))
		@if(\App\Domains\Entity\Enums\EntityEnum::tryFrom(setting('fal_ai_default_model', 'flux-pro')))
			{{ \App\Domains\Entity\Enums\EntityEnum::tryFrom(setting('fal_ai_default_model', 'flux-pro'))->label() }}
		@else
			Flux Pro
		@endif
    @elseif(class_exists('\App\Models\AiModel'))
		{{ \App\Models\AiModel::query()->where('key', setting('fal_ai_default_model', 'flux-pro'))?->value('selected_title') ?: 'Flux Pro'  }}
    @else
        Flux Pro
    @endif
</x-button>
