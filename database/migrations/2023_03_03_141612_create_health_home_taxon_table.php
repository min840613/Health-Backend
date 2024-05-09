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
        Schema::create('health_home_taxon', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->unsigned()->default(1)->comment('上下架 ; 1為上架，0為下架');
            $table->string('name', 50)->comment('前台顯示名稱');
            $table->integer('categories_id')->unsigned()->comment('主分類ID');
            $table->foreign('categories_id')->references('categories_id')->on('health_categories');
            $table->integer('article_id')->nullable()->unsigned()->comment('置頂文章ID');
            $table->foreign('article_id')->references('articles_id')->on('health_articles');
            $table->integer('sort')->unsigned()->nullable()->comment('排序ASC');
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
        Schema::dropIfExists('health_home_taxon');
    }
};
