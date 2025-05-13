<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $id = '',
        public string $name = '',
        public string $containerClass = '',
        public string $value = '',
        public string $label = '',
        public string $labelExtra = '',
        public string $type = 'text',
        public string $placeholder = '',
        public string $tooltip = '',
        public string $icon = '',
        public string $size = 'md',
        public string $selectOptions = '',
        public string $action = '',
        public bool $custom = false,
        // a style for checkbox and radio
        public bool $switcher = false,
        // add a 'Add New' option to the select with multiple options
        public bool $addNew = false,
        // for number input
        public bool $stepper = false
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.input');
    }
}
