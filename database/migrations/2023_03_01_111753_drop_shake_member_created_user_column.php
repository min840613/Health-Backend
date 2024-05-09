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
        Schema::table('health_shake_member', function (Blueprint $table) {
            $table->dropColumn(['created_user']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_shake_member', function (Blueprint $table) {
            $table->string('created_user', 50)->comment('建立者');
        });
    }
};
