<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SplitWords extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $text = '',
        public float $transitionDelayStart = 0,
        public float $transitionDelayStep = 0,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.split-words');
    }
}
