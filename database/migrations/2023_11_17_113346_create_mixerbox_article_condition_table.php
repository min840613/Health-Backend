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
        Schema::create('health_mixerbox_article_condition', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->comment('分類Id');
            $table->string('category_en_name')->comment('分類英文名');
            $table->string('category_name')->comment('分類中文名');
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->nullable()->comment('修改者');
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
        Schema::dropIfExists('health_mixerbox_article_condition');
    }
};
