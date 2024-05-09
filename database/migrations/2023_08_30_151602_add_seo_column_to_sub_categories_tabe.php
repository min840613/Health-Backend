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
        Schema::table('health_sub_categories', function (Blueprint $table) {
            $table->string('en_name', 50)->nullable()->after('name')->comment('子類別英文名稱');
            $table->string('meta_title', 255)->nullable()->after('en_name')->comment('Title');
            $table->string('description', 255)->nullable()->after('meta_title')->comment('Description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_sub_categories', function (Blueprint $table) {
            $table->dropColumn('en_name');
            $table->dropColumn('meta_title');
            $table->dropColumn('description');
        });
    }
};
