<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $type = 'default',
        public string $anchor = 'start',
        public string $title = '',
        public string $trigger = '',
        public string $modal = '',
        public string $headContent = '',
        public string $disableModalMessage = '',
        public bool $disableModal = false,
        public bool $disableFocus = false,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal');
    }
}
