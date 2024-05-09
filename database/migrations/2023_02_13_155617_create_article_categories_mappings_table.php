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
        Schema::create('health_article_categories_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('article_id')->comment('文章 id');
            $table->unsignedInteger('category_id')->comment('文章 id');
            $table->tinyInteger('sort')->default(0)->comment('0 為主要主分類');
            $table->unsignedInteger('parent')->nullable()->comment('null: 代表主分類');
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
        Schema::dropIfExists('health_article_categories_mappings');
    }
};
