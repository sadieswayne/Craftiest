<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AccordionItem extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $id = 'accordion-item-1',
        public string $style = '1',
        public string $title = 'How does it <span class="font-medium">generate responses?</span>',
        public string $content = '<p class="text-lg">Our support team will get assistance from AI-powered suggestions, making it quicker than ever to handle support requests.</p>',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.accordion-item');
    }
}
