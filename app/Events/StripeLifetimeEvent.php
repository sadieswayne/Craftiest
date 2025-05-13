<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StripeLifetimeEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $status;

    public $orderIds;

    /**
     * Create a new event instance.
     */
    public function __construct($status, $orderIds)
    {
        $this->status = $status;
        $this->orderIds = $orderIds;
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
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
