<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LqdIcon extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $size = 'md',
        public string $bg = '',
        public bool $activeBadge = false,
        public bool $activeBadgeCondition = false,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.lqd-icon');
    }
}
