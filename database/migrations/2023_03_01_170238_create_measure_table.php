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
        Schema::create('measure', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50)->nullable()->comment('標題');
            $table->string('link', 255)->nullable()->comment('URL');
            $table->string('image', 255)->nullable()->comment('主圖');
            $table->timestamp('start')->comment('開始時間');
            $table->timestamp('end')->comment('結束時間');
            $table->tinyInteger('status')->default(0)->comment('狀態');
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
        Schema::dropIfExists('measure');
    }
};
