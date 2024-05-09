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
        Schema::create('health_article_blocks', function (Blueprint $table) {
            $table->id();
            $table->integer('talent_id')->comment('達人ID');
            $table->tinyInteger('type')->comment('0: 名醫, 1: 食譜');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_article_blocks');
    }
};
