<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\UserSupport;

class TicketAction
{
    private string $sender;

    private array $message;

    private string $status;

    private string $activityType;

    private string $notifyTitle;

    public function __construct(private UserSupport $ticket) {}

    public static function ticket(UserSupport|int|string $ticketOrId): self
    {
        $ticket = $ticketOrId instanceof UserSupport ? $ticketOrId : UserSupport::findByTicketId($ticketOrId);

        return new self($ticket);
    }

    public function fromAdminIfTrue(bool $isAdmin): self
    {
        if ($isAdmin) {
            return $this->fromAdmin();
        }

        return $this->fromUser();
    }

    public function fromAdmin(): self
    {
        $this->sender = 'admin';

        return $this;
    }

    public function fromUser(): self
    {
        $this->sender = 'user';

        return $this;
    }

    public function answer(string $message): self
    {
        $this->status = $this->isSenderAdmin() ? 'Waiting for answer' : 'Answered';

        $this->activityType = __('Support request waiting for your answer');

        $this->notifyTitle = __('Support request answered');

        return $this->message($message);
    }

    public function new(string $message): self
    {
        $this->status = 'Submitted a Ticket';

        $this->activityType = __($this->status);

        $this->notifyTitle = __('Support request submitted');

        return $this->message($message);
    }

    private function message(string $message): self
    {
        $this->message = [
            'message' => $message,
            'sender'  => $this->sender,
        ];

        return $this;
    }

    public function send(): void
    {
        $this->updateStatus();

        $this->ticket->messages()->create($this->message);

        if ($this->isSenderAdmin()) {
            $this->sendNotify();
        } else {
            $this->createActivity();
        }
    }

    private function sendNotify(): void
    {
        Notify::to(
            $this->ticket->user,
            $this->notifyTitle,
            $this->message['message'],
            route('dashboard.support.view', $this->ticket->ticket_id)
        );
    }

    private function createActivity(): void
    {
        CreateActivity::for(
            $this->ticket->user,
            $this->activityType,
            $this->ticket->subject,
            route('dashboard.support.view', $this->ticket->ticket_id)
        );
    }

    private function isSenderAdmin(): bool
    {
        return $this->sender === 'admin';
    }

    private function updateStatus(): void
    {
        $this->ticket->update([
            'status' => $this->status,
        ]);

    }
}
