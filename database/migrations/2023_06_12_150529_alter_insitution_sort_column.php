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
            $table->integer('sort')->unsigned()->nullable()->after('nick_name')->comment('排序ASC');
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
            $table->dropColumn(['sort']);
        });
    }
};
