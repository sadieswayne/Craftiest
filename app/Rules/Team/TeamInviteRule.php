<?php

namespace App\Rules\Team;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TeamInviteRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $team = request()->route('team');

        $allow_seats = $team->allow_seats;

        $used_seats = $team->members()->count();

        if ($allow_seats <= $used_seats) {
            $fail(__('You have reached the maximum number of seats allowed for this team.'));
        }
    }
}
