<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extensions', function (Blueprint $table) {

            $columnsToDrop = collect([
                'name'        => 'extensions',
                'review'      => 'extensions',
                'description' => 'extensions',
                'category'    => 'extensions',
                'badge'       => 'extensions',
                'zip_url'     => 'extensions',
                'price_id'    => 'extensions',
                'image_url'   => 'extensions',
                'detail'      => 'extensions',
                'licensed'    => 'extensions',
                'theme_type'  => 'extensions',
                'price'       => 'extensions',
                'fake_price'  => 'extensions',
            ])->filter(function ($value, $key) {
                return Schema::hasColumn($value, $key);
            })->keys()->all();

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extensions', function (Blueprint $table) {
            //
        });
    }
};
