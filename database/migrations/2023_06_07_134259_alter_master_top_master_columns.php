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
        Schema::table('health_masters', function (Blueprint $table) {
            $table->unsignedBigInteger('division_id')->after('description')->comment('所屬醫院 id');
            $table->string('title')->after('division_id')->comment('目前職稱');
            $table->unsignedBigInteger('institution_id')->after('title')->comment('科別 id');
            $table->boolean('is_contracted')->after('institution_id')->default(0)->comment('簽約醫師');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_masters', function (Blueprint $table) {
            $table->string('en_name')->nullable(false)->change();
            $table->dropColumn(['division_id', 'title', 'institution_id', 'is_contracted']);
        });
    }
};
