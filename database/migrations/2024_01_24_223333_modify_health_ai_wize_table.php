<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('health_ai_wize', function (Blueprint $table) {
            $table->text('long_title')->change();
            $table->text('short_title')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_ai_wize', function (Blueprint $table) {
            $table->string('long_title')->change();
            $table->string('short_title')->change();
        });
    }
};
