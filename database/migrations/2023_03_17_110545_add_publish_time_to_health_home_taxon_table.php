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
        Schema::table('health_home_taxon', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->default(null)->comment('置頂文章上架時間')->after('article_id');
            $table->timestamp('published_end')->nullable()->default(null)->comment('置頂文章下架時間')->after('published_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_home_taxon', function (Blueprint $table) {
            $table->dropColumn(['published_at', 'published_end']);
        });
    }
};
