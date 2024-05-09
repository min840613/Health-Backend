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
        Schema::create('health_menu_list', function (Blueprint $table) {
            $table->increments('menu_list_id');
            $table->tinyInteger('parentid')->unsigned()->default(2)->comment('program_info_id 節目id關聯');
            $table->string('title', 50)->comment('menu名稱');
            $table->string('url', 500)->nullable()->comment('連結; 分類英文名、自輸入網址');
            $table->tinyInteger('position')->default(1)->comment('第一層或第二層選單');
            $table->integer('categories_id')->nullable()->comment('主分類ID');
            $table->tinyInteger('blank')->nullable()->default(0)->comment('1=另開、0=同頁開啟');
            $table->tinyInteger('menu_list_status')->nullable()->default(0)->comment('狀態');
            $table->tinyInteger('is_app')->nullable()->default(0)->comment('判斷此筆MENU為APP用');
            $table->tinyInteger('layout')->unsigned()->default(0)->comment('app專用欄位');
            $table->integer('sort')->nullable()->default(0)->comment('排序');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->comment('修改者');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['menu_list_id', 'categories_id', 'sort']);
            $table->index('url');
            $table->index('is_app');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_menu_list');
    }
};
