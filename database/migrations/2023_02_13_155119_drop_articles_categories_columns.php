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
        Schema::table('health_articles', function (Blueprint $table) {
            $table->dropColumn(['categories_main', 'categories_id', 'sub_categories_id', 'talent_recipe_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_articles', function (Blueprint $table) {
            $table->unsignedInteger('categories_main')->after('author_type')->default(0)->comment('主要分類id');
            $table->string('categories_id',50)->after('categories_main')->comment('分類(逗點紀錄)，排序第一位的為主分類');
            $table->integer('talent_recipe_id')->after('talent_category_id')->default(0)->comment('食譜達人ID(下拉連動選單)');
            $table->integer('sub_categories_id')->after('talent_recipe_id')->nullable()->comment('子分類ID');
        });
    }
};
