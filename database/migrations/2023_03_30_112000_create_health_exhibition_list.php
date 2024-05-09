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
        if (app()->environment('testing')) {

            Schema::connection('mysql_tvbs_2022')->dropIfExists('health_exhibition_list');

            Schema::connection('mysql_tvbs_2022')->create('health_exhibition_list', function (Blueprint $table) {
                $table->id();
                $table->string('title', 128)->comment('策展名稱');
                $table->integer('ip')->comment('IP 1-新聞 2-女大 3-食尚 4-健康 5-國際+');
                $table->string('image', 256)->nullable()->comment('策展主圖');
                $table->string('web_url', 256)->comment('web連結');
                $table->string('app_url', 256)->comment('app連結');
                $table->tinyInteger('blank')->comment('開啟方式 0-策展內開 1-策展外開');
                $table->timestamp('start_at')->nullable()->comment('上架時間');
                $table->timestamp('end_at')->nullable()->comment('下架時間');
                $table->integer('sort')->nullable()->comment('排序');
                $table->timestamps();
                $table->softDeletes();

                
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment('testing')) {
            Schema::connection('mysql_tvbs_2022')->dropIfExists('health_exhibition_list');
        }
    }
};
