<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LiveNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private $message,
        private $link = '#',
        private $title = null)
    {
        $this->message = $message;
        $this->link = $link ?? '#';
        $this->title = $title ?? __('New Notification');
    }

    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id'   => $notifiable->id,
            'user_name' => $notifiable->name,
            'data'      => [
                'message' => $this->message,
                'link'    => $this->link,
                'title'   => $this->title,
            ],
        ];
    }
}
