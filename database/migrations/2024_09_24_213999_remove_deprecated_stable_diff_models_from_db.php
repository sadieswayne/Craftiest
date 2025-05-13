<?php

use App\Helpers\Classes\EntityRemover;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $keysToDelete = [
            'stable-diffusion-xl-1024-v0-9',
            'stable-diffusion-xl-beta-v2-2-2',
            'stable-diffusion-512-v2-1',
        ];

        foreach ($keysToDelete as $entitySlug) {
            EntityRemover::removeEntity($entitySlug);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
