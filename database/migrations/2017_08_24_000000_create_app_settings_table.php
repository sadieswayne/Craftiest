<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppSettingsTable extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->index();
            $table->text('value');
        });
    }

    public function down(): void
    {
        Schema::drop('app_settings');
    }
}
