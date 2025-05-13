<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Helpers\Classes\InstallationHelper;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        InstallationHelper::runInstallation();
        $this->call([
            IntroductionSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            AdminPermissionSeeder::class,
        ]);

        $this->command->info('Currency table seeded!');
    }
}
