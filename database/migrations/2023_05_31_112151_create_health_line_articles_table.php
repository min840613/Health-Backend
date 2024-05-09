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
        Schema::create('health_line_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('article_id')->comment('文章ID');
            $table->tinyInteger('status')->default(0)->comment('供稿狀態; 0為否、1為是');
            $table->dateTime('release_date')->nullable()->comment('供稿時間');
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
        Schema::dropIfExists('health_line_articles');
    }
};
