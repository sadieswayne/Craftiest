<?php

namespace App\Listeners;

use App\Events\UsersActivityEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class UsersActivityListener implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;

    public ?string $queue = 'default';

    public int $delay = 0;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UsersActivityEvent $event): void
    {
        DB::table('users_activity')->insert([
            'email'      => $event->email,
            'type'       => $event->type ?? 'user',
            'ip'         => $event->ip,
            'connection' => $event->connection,
            'created_at' => now(),
        ]);
    }
}
