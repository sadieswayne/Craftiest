<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            $this->convertMyISAMToInnoDB();
        }
    }

    private function convertMyISAMToInnoDB(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $tables = DB::select("SHOW TABLE STATUS WHERE Engine = 'MyISAM'");
        foreach ($tables as $table) {
            try {
                DB::statement("ALTER TABLE `{$table->Name}` ENGINE=InnoDB;");
            } catch (Exception $e) {
                // ignore
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
