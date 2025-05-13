<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('affiliate_code')->nullable();
            $table->string('affiliate_earnings')->default(0);
            $table->text('affiliate_bank_account')->nullable();
            $table->unsignedBigInteger('affiliate_id')->nullable();
            $table->foreign('affiliate_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (! isDBDriverSQLite()) {
                $table->dropForeign(['affiliate_id']);
            }

            $table->dropColumn([
                'affiliate_code',
                'affiliate_bank_account',
                'affiliate_earnings',
                'affiliate_id',
            ]);
        });
    }
};
