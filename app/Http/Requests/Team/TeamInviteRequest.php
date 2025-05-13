<?php

namespace App\Http\Requests\Team;

use App\Rules\Team\TeamInviteRule;
use Illuminate\Foundation\Http\FormRequest;

class TeamInviteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'unique:team_members,email',
                'unique:users,email',
                new TeamInviteRule,
            ],
            'team_id' => 'required|exists:teams,id',
        ];
    }
}
