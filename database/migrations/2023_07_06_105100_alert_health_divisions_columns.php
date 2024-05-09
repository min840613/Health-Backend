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
        Schema::table('health_divisions', function (Blueprint $table) {
            $table->string('icon_hover', 255)->after('icon')->comment('Icon Hover URL');
            $table->string('icon_android_hover', 255)->nullable()->after('icon_android')->comment('Android PNG Icon Hover URL');
            $table->string('icon_ios_hover', 255)->nullable()->after('icon_ios')->comment('IOS PDF Icon Hover URL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_divisions', function (Blueprint $table) {
            $table->dropColumn(['icon_hover', 'icon_android_hover', 'icon_ios_hover']);
        });
    }
};
