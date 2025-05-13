<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TwoCheckoutWebhookEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $payload;

    /**
     * Create a new event instance.
     */
    public function __construct($payload)
    {
        //
        $this->payload = $payload;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(1),
        ];
    }
}
