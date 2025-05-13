<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Box extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $style = '1',
        public string $title = 'AI Generator',
        public string $desc = 'Generate <strong>text, image, code, chat</strong> and even more with MagicAI.',
        public string $badge = '',
        public string $wrapperClass = '',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.box');
    }
}
