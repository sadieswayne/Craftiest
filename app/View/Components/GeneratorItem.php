<?php

namespace App\View\Components;

use App\Models\OpenAIGenerator;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GeneratorItem extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public OpenAIGenerator $item
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.generator-item');
    }
}
