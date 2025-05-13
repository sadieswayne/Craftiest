<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class ModelSelectListWithChangeAlert extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?bool $bedrockOptions = false,
        public ?string $listLabel = '',
        public ?string $listId = '',
        public ?Collection $drivers = null,
        public ?string $currentModel = '',
        public ?bool $fineModelOptions = false,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.model-select-list-with-change-alert');
    }
}
