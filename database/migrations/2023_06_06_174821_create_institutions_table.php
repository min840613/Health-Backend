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
        Schema::create('health_institutions', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->unsigned()->default(1)->comment('上下架 ; 1為上架，0為下架');
            $table->string('name', 255)->comment('醫學中心名稱');
            $table->string('nick_name', 20)->comment('醫學中心簡稱');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->comment('修改者');
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
        Schema::dropIfExists('health_institutions');
    }
};
