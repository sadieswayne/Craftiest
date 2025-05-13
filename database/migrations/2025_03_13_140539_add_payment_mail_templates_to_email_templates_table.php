<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sqlFilePath = resource_path('dev_tools/subscription_and_payment_email_templates.sql');
        if (! file_exists($sqlFilePath)) {
            throw new \RuntimeException("SQL file not found: {$sqlFilePath}");
        }
        $sql = file_get_contents($sqlFilePath);
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            //
        });
    }
};
