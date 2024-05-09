<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('daile_view_count', 'daily_views_count');
        Schema::table('daily_views_count', function (Blueprint $table) {
            $table->dropColumn(['prg_id', 'created_at', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('daily_views_count', 'daile_view_count');
        Schema::table('daile_view_count', function (Blueprint $table) {
            $table->tinyInteger('prg_id')->default(14)->comment('健康為14');
            $table->timestamps();
        });
    }
};
