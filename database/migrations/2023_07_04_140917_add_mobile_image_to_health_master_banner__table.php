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
        Schema::table('health_master_banner', function (Blueprint $table) {
            $table->string('mobile_image', 500)->after('image')->comment('mobile主視覺路徑');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_master_banner', function (Blueprint $table) {
            $table->dropColumn('mobile_image');
        });
    }
};
