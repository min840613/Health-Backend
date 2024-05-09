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
        Schema::table('health_divisions', function (Blueprint $table) {
            $table->tinyInteger('type')->after('status')->unsigned()->default(1)->comment('1: 一般科別 2: 特殊科別');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_divisions', function (Blueprint $table) {
            $table->dropColumn(['type']);
        });
    }
};
