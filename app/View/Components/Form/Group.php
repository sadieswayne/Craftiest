<?php

declare(strict_types=1);

namespace App\View\Components\Form;

use Closure;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\Component;

class Group extends Component
{
    public function __construct(
        // public ViewErrorBag $errors,
        public ?string $id = null, // awareable
        public ?string $error = null, // awareable
        public ?string $setting = null, // awareable
        public ?string $help = null, // awareable
        public ?string $icon = null, // awareable
        public ?string $stepper = null, // awareable
        public ?string $action = null, // awareable
        public ?string $size = 'md',  // awareable
        public ?string $tooltip = null,  // awareable
        public ?string $label = null,  // awareable
        public bool $noGroupLabel = false,  // awareable
        public string $appendToLabel = ''
    ) {
        $this->handleErrors()
            ->applySettingField()
            ->applyLabel();
    }

    protected function handleAttributes(): void
    {
        $this->handleLivewireErrors();
    }

    protected function handleLivewireErrors(): void
    {
        if (! $this->attributes->has('wire:model')) {
            return;
        }

        if ($wireModel = $this->attributes->wire('model')) {
            $this->error = $this->getErrorInBag($wireModel->name());
        }
    }

    public function render(): Closure
    {
        return function (array $data) {
            $this->handleAttributes();

            return view('components.form.group');
        };
    }

    private function applySettingField(): static
    {
        if (! $this->setting || $this->label) {
            return $this;
        }

        [$settingGroup, $settingKey] = explode('.', $this->setting);

        $this->label = setting()::group($settingGroup)->label($settingKey);

        return $this;

    }

    private function applyLabel(): void
    {
        if ($this->appendToLabel !== '') {
            $this->label .= ' ' . $this->appendToLabel;
        }
    }

    private function getErrorInBag(string $errorKey): ?string
    {
        /** @var \Illuminate\Support\ViewErrorBag $errors */
        $errors = view()->shared('errors');

        if ($errors && $errors->has($errorKey)) {
            return $errors->first($errorKey);
        }

        return null;
    }

    private function handleErrors(): static
    {
        if ($this->error) {
            $this->error = $this->getErrorInBag($this->error);
        }

        if ($this->setting && ($error = $this->getErrorInBag($this->setting))) {
            $this->error = $error;
        }

        return $this;
    }
}
