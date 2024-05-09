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
        Schema::create('health_master_divisions', function (Blueprint $table) {
            $table->unsignedBigInteger('master_id')->comment('專家 id');
            $table->unsignedBigInteger('division_id')->comment('科別 id');
            $table->string('description')->nullable()->comment('科別細項');
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
        Schema::dropIfExists('health_master_divisions');
    }
};
