<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Notifications\LiveNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class Notify
{
    public static function to(
        User|int|string $userOrId,
        string $title,
        string $message,
        ?string $link = '#'
    ): void {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);

        $user->notify(new LiveNotification($message, $link, $title));
    }

    /**
     * @param  array<int, User>  $users
     */
    public static function toMany(
        array|Collection $users,
        string $title,
        string $message,
        ?string $link = '#'
    ): void {
        Notification::send($users, new LiveNotification($message, $link, $title));
    }
}
