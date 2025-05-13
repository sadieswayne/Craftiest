<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SectionHeader extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $width = 'w-2/5',
        public string $mb = '14',
        public string $title = 'The future of AI.',
        public string $subtitle = 'MagicAI is designed to help you generate high-quality content instantly, without breaking a sweat.'
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.section-header');
    }
}
