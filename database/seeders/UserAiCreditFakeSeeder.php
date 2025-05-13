<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserAiCreditFakeSeeder extends Seeder
{
    public function run(): void
    {

        User::query()->first()?->update([
            'entity_credits' => User::getFreshCredits(100),
        ]);
    }
}
