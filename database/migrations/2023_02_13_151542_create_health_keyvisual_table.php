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
        Schema::create('health_keyvisual', function (Blueprint $table) {
            $table->increments('keyvisual_id');
            $table->bigInteger('source_id')->nullable()->comment('來源ID');
            $table->string('type', 50)->nullable()->comment('主分類，對應：health_categories["en_name"]');
            $table->string('title', 50)->nullable()->comment('編輯推薦標題');
            $table->string('link', 255)->nullable()->comment('網址路徑');
            $table->string('image', 255)->nullable()->comment('圖片');
            $table->string('app_image', 255)->nullable()->comment('APP用的主圖');
            $table->timestamp('start')->comment('開始時間');
            $table->timestamp('end')->comment('結束時間');
            $table->tinyInteger('status')->default(1)->comment('狀態');
            $table->Integer('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->nullable()->comment('修改者');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_keyvisual');
    }
};
