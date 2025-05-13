<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Chatbot\Chatbot;
use App\Rules\DomainRule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ChatbotDomains extends Component
{
    public Collection $domains;

    public Chatbot $chatbot;

    public bool $addDomainDialog = false;

    public ?string $domain = null;

    public bool $addDomainButton = false;

    protected $listeners = [
        'refreshDomains' => 'setDomains',
    ];

    public function mount(Chatbot $chatbot): void
    {
        $chatbot->refresh();

        $this->chatbot = $chatbot;

        $this->setDomains();
    }

    public function setDomains(): void
    {
        $this->domains = $this->chatbot->domains ?? collect();
        $this->addDomainButton = $this->chatbot->canNewDomainAdd();
    }

    public function showModal(): void
    {
        $this->domain = null;
        $this->addDomainDialog = true;
    }

    public function closeModal(): void
    {
        $this->domain = null;
        $this->addDomainDialog = false;
    }

    public function rules(): array
    {
        return [
            'domain' => [
                'required',
                new DomainRule(true),
                Rule::unique('domains')->where('chatbot_id', $this->chatbot->id),
            ],
        ];
    }

    public function getValidationAttributes(): array
    {
        return [
            'domain' => __('Domain'),
        ];
    }

    public function addDomain(): void
    {
        $this->validate();

        // $domains = $this->chatbot->domains();

        if (! $this->chatbot->canNewDomainAdd()) {
            return;
        }

        $this->chatbot->domains()->create([
            'domain'  => $this->domain,
            'app_key' => Str::uuid(),
        ]);

        $this->resetErrorBag();

        $this->dispatch('confirming-add-domain');

        $this->closeModal();

        $this->dispatch('refreshDomains')->self();
    }

    public function render(): View
    {
        return view('livewire.chatbot.domains');
    }
}
