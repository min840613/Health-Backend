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
        Schema::create('deepq_keyword_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('keyword_id')->comment('deepq keyword id');
            $table->string('question')->comment('生成問題');
            $table->mediumInteger('sort')->comment('排序');
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
        Schema::dropIfExists('deepq_keyword_questions');
    }
};
