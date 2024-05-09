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
        Schema::create('health_system_announcement', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->comment('標題');
            $table->text('content')->comment('系統公告內容');
            $table->string('image_url', 200)->comment('圖片URL');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->nullable()->comment('修改者');
            $table->timestamps();
            $table->comment('系統公告');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_system_announcement');
    }
};
