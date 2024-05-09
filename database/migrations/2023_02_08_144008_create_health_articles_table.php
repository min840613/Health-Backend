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
        Schema::create('health_articles', function (Blueprint $table) {
            $table->increments('articles_id');
            $table->integer('articles_status')->comment('審核狀態');
            $table->dateTime('publish')->comment('發佈時間');
            $table->string('title', 200)->comment('標題')->comment('標題');
            $table->string('og_title', 200)->default('')->comment('社群標題');
            $table->string('seo_title', 200)->default('')->comment('seo_title');
            $table->integer('author')->comment('上稿者(下拉連動選單)');
            $table->tinyInteger('author_type')->nullable()->comment('作者1:報導 2:整理');
            $table->unsignedInteger('categories_main')->default(0)->comment('主要分類id');
            $table->string('categories_id', 50)->comment('分類(逗點紀錄)，排序第一位的為主分類');
            $table->integer('medicine_article_category_id')->nullable()->comment('醫學百科分類，對應：health_article_categories["id"]');
            $table->integer('talent_category_id')->default(0)->comment('名醫ID(下拉連動選單)');
            $table->integer('talent_recipe_id')->default(0)->comment('食譜達人ID(下拉連動選單)');
            $table->integer('sub_categories_id')->nullable()->comment('子分類ID');
            $table->tinyInteger('adult_flag')->default(0)->comment('18禁文章 0-否 1-是');
            $table->string('image', 255)->comment('主圖URL');
            $table->string('image_alt', 100)->comment('主圖圖說');
            $table->string('ogimage', 255)->comment('For FB/LINE分享圖用');
            $table->string('video_type', 30)->default('youtube')->comment('主影音分類(暫定youtube)');
            $table->string('video_id', 150)->comment('主影音(Youtube)');
            $table->string('fb_ia_video', 512)->comment('FB IA');
            $table->string('tag', 500)->comment('tag'); // 討論後由 1000 調整為 500
            $table->string('match_searchs', 300)->comment('前台模糊搜尋');
            $table->string('extended_article', 100)->comment('延伸閱讀');
            if (app()->environment('testing')) {
                $table->text('article_content')->comment('內容');
            } else {
                $table->text('article_content')->fulltext()->comment('內容');
            }
            $table->string('attractions', 1000)->nullable()->comment('店家');
            $table->string('description', 255)->comment('簡介(分類頁用)');
            $table->string('activity_id', 100)->comment('優惠活動');
            $table->string('product_id', 100)->comment('商品設定');
            $table->tinyInteger('is_line_rss')->comment('line 供稿');
            $table->string('video_file_name', 512)->comment('LINE RSS 影片名稱');
            $table->tinyInteger('is_zimedia')->nullable()->comment('1:供稿 2:下架');
            $table->tinyInteger('is_yahoo_rss')->comment('Yahoo 供稿');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->nullable()->comment('修改者');
            $table->string('match_url', 100)->comment('2017舊版配對URL');
            $table->string('external_source', 50)->comment('其他網站來源');
            $table->string('external_link', 255)->comment('其他網站連結');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['articles_id', 'articles_status']);
            $table->index('categories_id');
            $table->index('match_searchs');
            $table->index('tag');
            $table->index('talent_category_id');
            $table->index('title');
            $table->index(['articles_status', 'publish']);
            $table->comment('文章總覽');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_articles');
    }
};
