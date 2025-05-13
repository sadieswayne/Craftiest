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
        EntityRemover::removeEntity('runway-gen3');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
