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
        Schema::table('health_masters', function (Blueprint $table) {
            $table->unsignedBigInteger('division_id')->nullable(true)->change();
            $table->string('title')->nullable(true)->change();
            $table->unsignedBigInteger('institution_id')->nullable(true)->change();
            $table->boolean('is_contracted')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_masters', function (Blueprint $table) {
            $table->unsignedBigInteger('division_id')->nullable(false)->change();
            $table->string('title')->nullable(false)->change();
            $table->unsignedBigInteger('institution_id')->nullable(false)->change();
            $table->boolean('is_contracted')->nullable(false)->change();
        });
    }
};
