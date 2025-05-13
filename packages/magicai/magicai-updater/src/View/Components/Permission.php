<?php

namespace MagicAI\Updater\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Permission extends Component
{
    public function __construct(public bool $permission) {}

    public function render(): View
    {
        return view('magicai-updater::components.permission', [
            'permission' => $this->permission,
        ]);
    }
}
