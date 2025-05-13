<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EngineSelectListWithChangeAlert extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?bool $isAW = false,
        public ?string $listLabel = '',
        public ?string $listId = '',
        public ?array $engines = [],
        public ?string $currentEngine = '',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.engine-select-list-with-change-alert');
    }
}
