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
        Schema::create('health_shake_member', function (Blueprint $table) {
            $table->id();
            $table->string('xnm_id')->comment('IBM M_ID');
            $table->string('xnmail')->comment('註冊email');
            $table->string('xnbirthday')->comment('生日');
            $table->string('xnnickname')->comment('暱稱');
            $table->string('xnphone')->comment('手機號碼');
            $table->string('xnsex')->comment('性別');
            $table->string('xnaddress')->comment('地址');
            $table->dateTime('xn_time_activity')->comment('參加時間');
            $table->dateTime('xn_time_mk')->comment('建立時間');
            $table->integer('shake_id')->unsigned()->comment('活動id');
            $table->tinyInteger('shake_status')->unsigned()->nullable()->comment('搖一搖類型 0=>失敗 , 1=>成功 , 2=>直接進入');
            $table->text('profile_data')->collation('utf8_unicode_ci')->nullable();
            $table->text('profile')->collation('utf8_unicode_ci')->nullable();
            $table->string('error_code', 10);
            $table->string('update_source', 30)->nullable()->comment('修改資料功能');
            $table->string('app_version', 50)->nullable();
            $table->string('device_code')->nullable();
            $table->tinyInteger('pass_status')->nullable();
            $table->string('acr')->nullable();
            $table->string('created_user', 50)->comment('建立者');
            $table->string('updated_user', 50)->nullable()->comment('修改者');
            $table->timestamps();
            $table->comment('APP-搖一搖參於者');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_shake_member');
    }
};
