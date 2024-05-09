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
        Schema::create('keyword_click_count', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('點擊發生日');
            $table->string('keyword')->comment('關鍵字');
            $table->bigInteger('click_count')->comment('點擊數');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keyword_click_count');
    }
};
