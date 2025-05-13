<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\SendPaymentConfirmationEmail;
use App\Models\Plan;
use App\Models\User;

final class EmailPaymentConfirmation
{
    private function __construct(private readonly User $user, private readonly Plan $plan) {}

    public static function create(User $user, Plan $plan): self
    {
        return new self($user, $plan);
    }

    public function send(): void
    {
        if ($this->shouldSend()) {
            $this->dispatch();
        }
    }

    public function resend(): void
    {
        $this->send();
    }

    private function dispatch(): void
    {
        SendPaymentConfirmationEmail::dispatch($this->user, $this->plan);
    }

    public function shouldSend(): bool
    {
        return (int) setting('send_payment_confirmation', 1) === 1;
    }

    private function lockKey(): string
    {
        return 'send_payment_confirmation_email_' . $this->user->id;
    }
}
