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
        Schema::table('health_masters', function (Blueprint $table) {
            $table->string('content_image')->nullable()->after('image')->comment('醫師內容圖片');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_masters', function (Blueprint $table) {
            $table->dropColumn(['content_image']);
        });
    }
};
