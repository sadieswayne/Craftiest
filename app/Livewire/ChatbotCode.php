<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Chatbot\Domain;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ChatbotCode extends Component
{
    public Domain $domain;

    public bool $showCodeDialog = false;

    public function mount(Domain $domain): void
    {
        $this->domain = $domain;
    }

    public function showCodeModal(): void
    {
        $this->showCodeDialog = true;
    }

    public function closeCodeModal(): void
    {
        $this->showCodeDialog = false;
    }

    private function getScriptCode(): string
    {
        return '<script type="module" src="https://cdn.projecthub.ai/min/chatbot.min.js"></script>';
    }

    private function getEmbedCode(): string
    {
        $appDomain = parse_url(config('app.url'), PHP_URL_HOST);

        return sprintf('<magicai-chatbot domain="%s" app-key="%s"></magicai-chatbot>', $appDomain, $this->domain->app_key);
    }

    public function render(): View
    {
        return view('livewire.chatbot.code', [
            'scriptCode' => $this->getScriptCode(),
            'embedCode'  => $this->getEmbedCode(),
            'appKey'     => $this->domain->app_key,
        ]);
    }
}
