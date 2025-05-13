<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Chatbot\Domain;
use App\Rules\DomainRule;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ChatbotDomain extends Component
{
    public Domain $domain;

    public string $editingDomain = '';

    public bool $editDomainDialog = false;

    public bool $deleteDomainDialog = false;

    public function mount(Domain $domain): void
    {
        $domain->refresh();

        $this->domain = $domain;
        $this->editingDomain = $domain->domain;
    }

    public function rules(): array
    {
        return [
            'editingDomain' => [
                'required',
                new DomainRule(true),
                Rule::unique('domains', 'domain')->ignore($this->domain->id)->where('chatbot_id', $this->domain->chatbot->id),
            ],
        ];
    }

    public function getValidationAttributes(): array
    {
        return [
            'editingDomain' => __('Domain'),
        ];
    }

    public function showModal(): void
    {
        $this->editingDomain = $this->domain->domain;
        $this->editDomainDialog = true;
    }

    public function showDeleteModal(): void
    {
        $this->editingDomain = $this->domain->domain;
        $this->deleteDomainDialog = true;
    }

    public function closeModal(): void
    {
        $this->editingDomain = '';
        $this->editDomainDialog = false;
        $this->deleteDomainDialog = false;
    }

    public function delete(): void
    {
        $this->domain?->delete();
        $this->refreshList();
    }

    public function refreshList(): void
    {
        $this->dispatch('refreshDomains');
    }

    public function editDomain(): void
    {
        $this->validate();

        $this->domain->update([
            'domain' => $this->editingDomain,
        ]);

        $this->resetErrorBag();

        $this->dispatch('confirming-edit-domain');

        $this->closeModal();
        $this->refreshList();
    }

    public function render(): View
    {
        return view('livewire.chatbot.domain');
    }
}
