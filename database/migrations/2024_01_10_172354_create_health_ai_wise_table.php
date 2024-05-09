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
        Schema::create('health_ai_wize', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ai_wize_id')->comment('AI Wize ID');
            $table->dateTime('ai_wize_publish')->comment('AI Wize publish time');
            $table->unsignedInteger('health_article_id')->nullable()->comment('health article ID');
            $table->string('long_title')->nullable()->comment('長標題');
            $table->string('short_title')->nullable()->comment('短標題');
            $table->text('content')->comment('內容');
            $table->string('keyword')->nullable()->comment('關鍵字');
            $table->string('choose_user')->nullable()->comment('選取者');
            $table->tinyInteger('status')->default(1)->comment('狀態');
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
        Schema::dropIfExists('health_ai_wize');
    }
};
