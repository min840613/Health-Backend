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
        Schema::table('health_articles', function (Blueprint $table) {
            // 因change方式無法是用tinyInteger，所以使用Integer
            $table->unsignedInteger('is_line_rss')->default(0)->comment('Line Video 供稿 ; 1為是，0為否')->change();
            $table->unsignedInteger('is_yahoo_rss')->default(0)->comment('Yahoo 供稿 ; 1為是，0為否')->change();
            $table->unsignedTinyInteger('is_line_article')->default(0)->after('is_line_rss')->comment('Line Article 供稿 ; 1為是，0為否');
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
            $table->dropColumn('is_line_article');
        });
    }
};
