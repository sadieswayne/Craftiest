<?php

namespace App\View\Components\Navbar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Link extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $href = '',
        public string $slug = '',
        public string $label = 'Menu Link',
        public string $icon = '',
        public string $iconHtml = '',
        public string $activeCondition = '',
        public string $badge = '',
        public bool $localizeHref = false,
        public bool $new = false,
        public bool $letterIcon = false,
        public string $letterIconStyles = '',
        public bool $dropdownTrigger = false,
        public string $triggerType = '',
        public string $modal = '',
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navbar.link');
    }
}
