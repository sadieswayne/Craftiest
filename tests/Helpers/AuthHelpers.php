<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

function loginAsUser(User|array|null $userOrAttributes = null, Roles $role = Roles::USER): User
{
    if ($userOrAttributes instanceof User) {
        $user = $userOrAttributes;
    } else {
        $user = User::factory()->create($userOrAttributes ?? []);
    }

    Auth::login($user);

    return $user;
}

function loginAsAdmin(User|array|null $userOrAttributes = null): User
{
    return loginAsUser($userOrAttributes, Roles::ADMIN);
}
