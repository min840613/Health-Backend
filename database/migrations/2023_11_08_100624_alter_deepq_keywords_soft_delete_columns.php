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
        Schema::table('deepq_keywords', function (Blueprint $table) {
            $table->string('deleted_user')->after('updated_user')->nullable()->comment('刪除者');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deepq_keywords', function (Blueprint $table) {
            $table->dropColumn(['deleted_user', 'deleted_at']);
        });
    }
};
