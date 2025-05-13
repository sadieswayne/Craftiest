<?php

namespace App\View\Components\Documents;

use App\Models\Folders;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Folder extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Folders $folder,
        public bool $folderSingleView = false
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.documents.folder');
    }
}
