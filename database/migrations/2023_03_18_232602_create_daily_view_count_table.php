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
        Schema::create('daile_view_count', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('prg_id')->default(14)->comment('健康為14');
            $table->date('date')->comment('點擊發生日');
            $table->string('source_type')->comment('文章來源');
            $table->bigInteger('source_id')->comment('文章來源ID');
            $table->bigInteger('click_count')->comment('點擊數');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daile_view_count');
    }
};
