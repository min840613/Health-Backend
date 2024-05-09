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
        Schema::create('daily_views_count_old', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('點擊發生日');
            $table->string('source_type')->comment('文章來源');
            $table->bigInteger('source_id')->comment('文章來源ID');
            $table->bigInteger('click_count')->comment('點擊數');
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
        Schema::dropIfExists('daily_views_count_old');
    }
};
