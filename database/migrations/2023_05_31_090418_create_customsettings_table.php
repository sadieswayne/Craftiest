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
        Schema::create('customsettings', function (Blueprint $table) {
            $table->id();
            $table->string('key');                                      // Unique key value of setting e.g. : howitworks_link
            $table->string('title')->nullable();                        // Short description to remember where/what for it is used
            $table->string('value_str')->nullable();                    // String value
            $table->text('value_text')->nullable();                     // Text value
            $table->longText('value_longtext')->nullable();             // Long Text value
            $table->text('value_html')->nullable();                     // HTML value (Cleans/Sanitizes in Model)
            $table->integer('value_int')->default(0);                   // Integer value
            $table->bigInteger('value_bigint')->nullable();             // Big integer value
            $table->unsignedBigInteger('value_ubigint')->nullable();    // Unsigned big integer value
            $table->double('value_double')->default(0);                 // Double value
            $table->boolean('value_bool')->default(false);              // Boolean value
            $table->date('value_date')->nullable();                     // Date value
            $table->timestamp('value_timestamp')->nullable();           // Timestamp value
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customsettings');
    }
};
