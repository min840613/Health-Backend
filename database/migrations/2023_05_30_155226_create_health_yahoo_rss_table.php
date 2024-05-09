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
        Schema::create('health_yahoo_rss', function (Blueprint $table) {
            $table->id();
            $table->integer('article_id')->comment('文章id');
            $table->unsignedTinyInteger('status')->default(1)->comment('供稿狀態');
            $table->dateTime('rss_release_date')->nullable()->comment('供稿日期');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->nullable()->comment('修改者');
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
        Schema::dropIfExists('health_yahoo_rss');
    }
};
