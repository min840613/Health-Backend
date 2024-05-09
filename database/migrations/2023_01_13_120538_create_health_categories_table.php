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
        Schema::create('health_categories', function (Blueprint $table) {
            $table->increments('categories_id');
            $table->tinyInteger('categories_type')->unsigned()->default(1)->comment('分類類型，1:內容分類、2:業務分類、3:廣編分類');
            $table->timestamp('publish')->useCurrent()->comment('發佈時間');
            $table->string('name', 50)->comment('類別名稱');
            $table->string('en_name', 50)->unique()->comment('類別英文名稱');
            $table->string('meta_title', 255)->nullable()->comment('Title');
            $table->string('description', 255)->nullable()->comment('Description');
            $table->string('image', 200)->nullable()->comment('icon url');
            $table->tinyInteger('categories_status')->unsigned()->default(0)->comment('狀態');
            $table->integer('show_category_menu')->unsigned()->default(0);
            $table->integer('sort_index')->unsigned()->default(0)->comment('排序');
            $table->tinyInteger('target')->unsigned()->default(0)->comment('另開新視窗，1:是、0:否');
            $table->tinyInteger('is_nav')->unsigned()->default(1)->comment('是否在導覽列，1:是、0:否');
            $table->integer('index_position')->unsigned()->default(0)->comment('在首頁分類單元顯示順序(1,2,3......)');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->comment('修改者');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['categories_id', 'en_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_categories');
    }
};
