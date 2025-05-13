<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ColorBox extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title = 'Digital Agencies',
        public string $color = 'orange'
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.color-box');
    }
}
