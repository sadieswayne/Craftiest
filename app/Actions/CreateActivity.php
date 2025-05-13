<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Services\Common\MenuService;

class CreateActivity
{
    public static function for(
        User|int|string $userOrId,
        string $activity_type,
        string $activity_title,
        ?string $url = null
    ): void {
        $user = $userOrId instanceof User ? $userOrId : User::findOrFail($userOrId);

        $user->activities()->create([
            'activity_type'  => $activity_type,
            'activity_title' => $activity_title,
            'url'            => $url,
        ]);

        app(MenuService::class)->regenerate();

        Notify::toMany(
            User::admins()->get(),
            $activity_type . ' "' . $activity_title . '"',
            $user?->fullName(),
            $url
        );
    }
}
