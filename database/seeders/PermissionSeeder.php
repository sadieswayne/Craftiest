<?php

namespace Database\Seeders;

use App\Enums\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Permissions::cases() as $permission) {
            $this->createPermission($permission->value, 'web');
        }
    }

    private function createPermission(string $name, string $guardName): void
    {
        Permission::query()
            ->firstOrCreate([
                'name'       => $name,
                'guard_name' => $guardName,
            ]);
    }
}
