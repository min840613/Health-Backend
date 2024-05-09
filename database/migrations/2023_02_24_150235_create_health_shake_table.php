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
        Schema::create('health_shake', function (Blueprint $table) {
            $table->id('shake_id');
            $table->tinyInteger('shake_status')->default('1')->nullable()->comment('開關');
            $table->string('shake_title', 50)->nullable()->comment('標題');
            $table->string('content', 1000)->comment('文案內容');
            $table->string('shake_url', 100)->nullable()->comment('連結');
            $table->tinyInteger('shake_type')->unsigned()->comment('活動類型 1：電視活動，2：一般活動');
            $table->tinyInteger('shake_content_type')->default(1)->nullable()->comment('搖一搖開啟類型, 1=>URL,2=>精選主題文章ID,3=>吃喝玩樂文章ID');
            $table->tinyInteger('is_ec_connect')->unsigned()->comment('是否為EC連結');
            $table->dateTime('shake_time_start')->nullable()->comment('搖一搖活動開始時間');
            $table->dateTime('shake_time_end')->nullable()->comment('搖一搖活動結束時間');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->nullable()->comment('修改者');
            $table->timestamps();
            $table->comment('APP搖一搖');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_shake');
    }
};
