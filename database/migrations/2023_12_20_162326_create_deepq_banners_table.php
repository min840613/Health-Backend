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
        Schema::create('deepq_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title', 250)->comment('活動名稱');
            $table->string('image', 250)->comment('主圖路徑');
            $table->timestamp('start')->comment('活動開始時間');
            $table->timestamp('end')->comment('活動結束時間');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('狀態');
            $table->integer('sort')->unsigned()->default(0)->comment('排序ASC');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->comment('修改者');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deepq_banners');
    }
};
