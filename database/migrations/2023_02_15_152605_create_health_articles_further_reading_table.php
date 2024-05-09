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
        Schema::create('health_articles_further_reading', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('article_id')->comment('文章 id');
            $table->unsignedInteger('recommendation_article_id')->comment('關聯文章 id');
            $table->string('type')->comment('類別：article: 文章延伸閱讀,yahoo: yahoo供稿延伸閱讀');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->comment('修改者');
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
        Schema::dropIfExists('health_articles_further_reading');
    }
};
