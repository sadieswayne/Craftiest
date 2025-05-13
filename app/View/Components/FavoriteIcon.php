<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FavoriteIcon extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public bool $isFavorited = false,
        public string $idleIcon = 'tabler-star',
        public string $activeIcon = 'tabler-star-filled',
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.favorite-icon');
    }
}
