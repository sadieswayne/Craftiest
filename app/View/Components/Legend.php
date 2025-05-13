<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Legend extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $label = 'Label',
        public string $size = 'md',
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.legend');
    }
}
