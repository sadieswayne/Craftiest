<?php

namespace MagicAI\Updater\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public function __construct(
        public bool $permission,
        public string $text,
        public ?string $id = null,
    ) {}

    public function render(): View
    {
        return view('magicai-updater::components.button', [
            'permission' => $this->permission,
            'text'       => $this->text,
            'id'         => $this->id,
        ]);
    }
}
