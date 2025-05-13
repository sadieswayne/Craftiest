<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TabsTrigger extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $label = 'AI Text Generator',
        public string $style = '1',
        public string $target = '#',
        public string $badge = '',
        public bool $active = false
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tabs-trigger');
    }
}
