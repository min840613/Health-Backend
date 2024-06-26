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
        Schema::table('daily_views_count', function (Blueprint $table) {
            $table->index('source_id');
            $table->index('source_type');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_views_count', function (Blueprint $table) {
            $table->dropIndex(['source_id']);
            $table->dropIndex(['source_type']);
            $table->dropIndex(['date']);
        });
    }
};
