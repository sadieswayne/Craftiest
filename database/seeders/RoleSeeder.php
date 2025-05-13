<?php

namespace Database\Seeders;

use App\Enums\Roles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Roles::cases() as $role) {
            $this->createRole($role->value, 'web');
        }
    }

    private function createRole(string $name, string $guardName): void
    {
        Role::query()
            ->firstOrCreate([
                'name'       => $name,
                'guard_name' => $guardName,
            ]);
    }
}
