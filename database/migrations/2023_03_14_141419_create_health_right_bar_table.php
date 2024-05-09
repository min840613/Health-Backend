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
        Schema::create('health_right_bar', function (Blueprint $table) {
            $table->id();
            $table->string('name', 10)->comment('版位名稱');
            $table->integer('main_category')->unsigned()->comment('對應主分類');
            $table->integer('sub_category')->unsigned()->default(0)->comment('對應子分類，0為無對應子分類');
            $table->integer('article_require_master')->unsigned()->default(0)->comment('文章需含區塊，1為醫師、2為達人');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('上下架 ; 1為上架，0為下架');
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
        Schema::dropIfExists('health_right_bar');
    }
};
