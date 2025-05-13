<?php

namespace App\View\Components;

use App\Domains\Marketplace\Repositories\Contracts\ExtensionRepositoryInterface;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DemoSwitcher extends Component
{
    public array $themes;

    /**
     * Create a new component instance.
     */
    public function __construct(
        protected ExtensionRepositoryInterface $inter,
        public string $themesType = 'Frontend'|'Dashboard'|'All',
    ) {
        $this->themes = $inter->themes();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.demo-switcher');
    }
}
