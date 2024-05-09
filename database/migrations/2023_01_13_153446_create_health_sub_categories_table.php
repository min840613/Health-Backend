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
        Schema::create('health_sub_categories', function (Blueprint $table) {
            $table->increments('sub_categories_id');
            $table->integer('categories_id')->unsigned()->comment('主分類ID');
            $table->foreign('categories_id')->references('categories_id')->on('health_categories');
            $table->string('name', 50)->comment('子類別名稱');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->comment('修改者');
            $table->timestamps();
            $table->softDeletes();
            $table->index('categories_id');
            $table->index(['sub_categories_id', 'categories_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_sub_categories');
    }
};
