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
        Schema::create('deepq_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->comment('關鍵字');
            $table->dateTime('start_at')->comment('開始時間');
            $table->dateTime('end_at')->comment('結束時間');
            $table->mediumInteger('count')->nullable()->comment('刊登則數');
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
        Schema::dropIfExists('deepq_keywords');
    }
};
