<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Support\Chatbot\Embeddable;
use Illuminate\Contracts\View\View;
use Livewire\Component;

#[Embeddable]
class Chatbot extends Component
{
    public function render(): View
    {
        return view('livewire.chatbot.chatbot');
    }
}
