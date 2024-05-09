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
        Schema::table('health_sub_categories', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->default(1)->after('name')->comment('上下架 ; 1為上架，0為下架');
            $table->unsignedInteger('sort')->default(0)->after('status')->comment('排序ASC');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_sub_categories', function (Blueprint $table) {
            $table->dropColumn(['status', 'sort']);
        });
    }
};
