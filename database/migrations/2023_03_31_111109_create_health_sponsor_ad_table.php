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
        Schema::create('health_sponsor_ad', function (Blueprint $table) {
            $table->id();
            $table->integer('article_id')->comment('文章ID');
            $table->integer('categories_list_id')->comment('分類ID 0=首頁');
            $table->integer('position')->comment('廣編稿位置');
            $table->timestamp('start')->nullable()->comment('開始時間');
            $table->timestamp('end')->nullable()->comment('結束時間');
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
        Schema::dropIfExists('health_sponsor_ad');
    }
};
