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
        Schema::create('health_authors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('編輯名稱');
            $table->tinyInteger('type')->unsigned()->default(0)->comment('類別，0：一般上稿者，1：廣邊稿上稿者');
            $table->tinyInteger('status')->unsigned()->comment('狀態');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->comment('修改者');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_authors');
    }
};
