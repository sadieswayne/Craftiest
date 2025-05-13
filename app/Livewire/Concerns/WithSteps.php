<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

trait WithSteps
{
    public int $step = 1;

    private function getMaxStep(): int
    {
        if (property_exists($this, 'maxStep')) {
            return $this->maxStep;
        }
        if (method_exists($this, 'maxStep')) {
            return $this->maxStep();
        }

        return 1;
    }

    public function currentStep(): int
    {
        return $this->step;
    }

    public function totalStep(): int
    {
        return $this->getMaxStep();
    }

    public function currentStepIs(int $step): bool
    {
        return $this->step === $step;
    }

    public function toPreviousStep(): void
    {
        if (! $this->hasPreviousStep()) {
            return;
        }

        $this->changeStep($this->step - 1);
    }

    public function toNextStep(): void
    {
        if (! $this->hasNextStep()) {
            return;
        }

        $this->changeStep($this->step + 1);
    }

    public function hasNextStep(): bool
    {
        return $this->step < $this->getMaxStep();
    }

    public function hasPreviousStep(): bool
    {
        return $this->step > 1;
    }

    public function changeStep(int $step): void
    {
        $this->step = $step;
    }
}
