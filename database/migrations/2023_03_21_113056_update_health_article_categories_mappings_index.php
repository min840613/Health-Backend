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
        Schema::table('health_article_categories_mappings', function (Blueprint $table) {
            $table->index('article_id');
            $table->index('category_id');
            $table->index(['article_id', 'category_id', 'sort', 'parent'], 'searchArticlesIFrameKey');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_article_categories_mappings', function (Blueprint $table) {
            $table->dropIndex(['article_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex('searchArticlesIFrameKey');
        });
    }
};
