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
        Schema::create('health_articles_sickness', function (Blueprint $table) {
            $table->integer('article_id')->unsigned()->comment('文章id，對應：health_articles["articles_id"]');
            $table->foreign('article_id')->references('articles_id')->on('health_articles');
            $table->foreignId('health_sickness_id')->constrained('health_sickness');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->comment('修改者');
            $table->timestamps();
            $table->index(['article_id', 'health_sickness_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_articles_sickness');
    }
};
