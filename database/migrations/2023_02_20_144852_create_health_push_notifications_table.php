<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_push_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('source_id')->nullable()->comment('來源ID');
            $table->tinyInteger('type')->nullable()->default(2)->comment('1.文章 2.影片 3.訊息通知 4.活動公告 5.APP搖一搖 6.會員專屬 7.我的通知');
            $table->unsignedInteger('categories_id')->comment('分類ID');
            $table->string('category_en', 50)->comment('分類英文名稱');
            $table->string('category', 50)->nullable()->default('健康')->comment('分類中文名稱');
            $table->tinyInteger('push_notifications_status')->nullable()->default(1)->comment('發送狀態(1. 尚未推播 2. 推播成功 3. 取消推播 4. 推播失敗 5. 推播發送中)');
            $table->string('platform_type', 4)->nullable()->comment('web,app');
            $table->enum('member_group', ['全部','指定'])->nullable()->default('全部')->comment('會員專屬設定');
            $table->string('message', 1000)->comment('推播標題');
            $table->text('message_body')->nullable()->collation('utf8mb4_unicode_ci')->comment('推播內容');
            $table->tinyInteger('content_type')->comment('內容類型(1.url  2.內容)');
            $table->string('image', 255)->nullable()->comment('推播圖片');
            $table->string('url', 1000)->nullable()->comment('URL連結');
            $table->dateTime('prepush')->nullable()->comment('預約發送時間');
            $table->dateTime('pushed')->nullable()->comment('發送日期');
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
        Schema::dropIfExists('health_push_notifications');
    }
};
