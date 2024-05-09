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
        Schema::table('health_articles', function (Blueprint $table) {
            $table->integer('master_id')->nullable()->change();
            $table->dropColumn(['product_id','activity_id','description','attractions','external_source','external_link']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_articles', function (Blueprint $table) {
            $table->integer('master_id')->nullable(false)->change();
            // $table->column(['product_id','activity_id','description','attractions','external_source','external_link']);
        });
    }
};
