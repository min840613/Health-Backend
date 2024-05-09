<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Encyclopedia\SicknessToOrganModel;

class HealthSicknessToOrganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); //禁用外键约束
        DB::table('health_sickness_to_organ')->truncate();

        DB::transaction(function () {
            $result = DB::connection('mysql_tvbs_v4')->table('health_sickness_to_organ')->get();
            $result->transform(function($i) {
                return (array)$i;
            });
            $result = $result->toArray();
            SicknessToOrganModel::insert($result);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); //啟用外键约束
    }
}
