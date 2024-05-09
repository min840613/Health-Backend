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
        Schema::create('health_end_of_text', function (Blueprint $table) {
            $table->increments('text_id');
            $table->tinyInteger('text_type')->unsigned()->default(1)->comment('文字類型，1警語 ; 2廣宣');
            $table->string('short_title', 120)->comment('短標題');
            $table->string('url', 500)->nullable()->comment('自輸入網址');
            $table->text('content')->nullable()->comment('內容');
            $table->integer('order_num')->default(0)->comment('排序');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('狀態');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->comment('修改者');
            $table->timestamp('published_at')->nullable()->default(null)->comment('上架時間');
            $table->timestamp('published_end')->nullable()->default(null)->comment('下架時間');
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
        Schema::dropIfExists('health_end_of_text');
    }
};
