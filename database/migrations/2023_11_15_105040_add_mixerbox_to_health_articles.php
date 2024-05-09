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
            $table->tinyInteger('is_mixerbox_article')->after('is_line_rss')->comment('Mixerbox 供稿');
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
            $table->dropColumn('is_mixerbox_article');
        });
    }
};
