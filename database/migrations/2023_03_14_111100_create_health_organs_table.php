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
        Schema::create('health_organs', function (Blueprint $table) {
            $table->id();
            // $table->integer('body_id')->unsigned()->comment('health_body id');
            $table->foreignId('body_id')->constrained('health_body');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('上下架 ; 1為上架，0為下架');
            $table->string('name', 50)->comment('組織器官名稱');
            $table->string('icon', 255)->comment('Icon URL');
            $table->string('icon_android', 255)->nullable()->comment('Android PNG Icon URL');
            $table->string('icon_ios', 255)->nullable()->comment('IOS PDF Icon URL');
            $table->integer('sort')->unsigned()->nullable()->comment('排序ASC');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->comment('修改者');
            $table->timestamps();
            $table->index('body_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_organs');
    }
};
