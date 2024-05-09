<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table)
        {
            $table->Integer('employee_id')->nullable()->after('email')->comment('員編');
            $table->Integer('phone_extension')->nullable()->after('email')->comment('分機');
            $table->string('department_name',50)->nullable()->after('email')->comment('部門名稱');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table)
        {
            $table->dropColumn(['employee_id', 'phone_extension','department_name']);
        });
    }
}
