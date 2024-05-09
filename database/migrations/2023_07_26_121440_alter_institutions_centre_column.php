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
        Schema::table('health_institutions', function (Blueprint $table) {
            $table->boolean('is_centre')->after('nick_name')->default(0)->comment('是否為醫學中心');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_institutions', function (Blueprint $table) {
            $table->dropColumn(['is_centre']);
        });
    }
};
