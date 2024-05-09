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
        Schema::create('health_sickness_to_organ', function (Blueprint $table) {
            $table->foreignId('sickness_id')->constrained('health_sickness');
            $table->foreignId('organ_id')->constrained('health_organs');
            $table->index(['sickness_id', 'organ_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_sickness_to_organ');
    }
};
