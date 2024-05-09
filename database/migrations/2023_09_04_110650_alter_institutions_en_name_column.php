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
            $table->string('en_name', 30)->after('name')->unique()->nullable()->comment('英文名稱');
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
            $table->dropColumn(['en_name']);
        });
    }
};
