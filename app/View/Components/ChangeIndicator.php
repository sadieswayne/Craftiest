<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ChangeIndicator extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public float $value = 0
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.change-indicator');
    }
}
