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
        Schema::create('health_master_banner', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->unsigned()->default(0)->comment('類型; 0為內部連結、1為外部連結');
            $table->unsignedInteger('institution_id')->nullable()->comment('院所ID');
            $table->unsignedInteger('division_id')->nullable()->comment('科別ID');
            $table->unsignedInteger('master_id')->nullable()->comment('醫師ID');
            $table->string('url', 500)->nullable()->comment('外部連結');
            $table->string('image', 500)->comment('主視覺路徑');
            $table->timestamp('published_at')->nullable()->default(null)->comment('上架時間');
            $table->timestamp('published_end')->nullable()->default(null)->comment('下架時間');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('狀態; 0為下架、1為上架');
            $table->integer('sort')->unsigned()->default(0)->comment('排序，ASC');
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
        Schema::dropIfExists('health_master_banner');
    }
};
