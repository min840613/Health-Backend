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
        Schema::create('health_right_bar_detail', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->comment('標題');
            $table->unsignedBigInteger('right_bar_id')->comment('health_right_bar ID');
            $table->foreign('right_bar_id')->references('id')->on('health_right_bar');
            $table->integer('article_id')->unsigned()->comment('文章ID');
            $table->foreign('article_id')->references('articles_id')->on('health_articles');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('上下架 ; 1為上架，0為下架');
            $table->integer('sort')->unsigned()->nullable()->comment('排序ASC');
            $table->timestamp('published_at')->comment('上架時間');
            $table->timestamp('published_end')->comment('下架時間');
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
        Schema::dropIfExists('health_right_bar_detail');
    }
};
