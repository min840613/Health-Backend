<?php

namespace Database\Seeders;

use App\Helpers\UrlHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Encyclopedia\OrgansModel;

class HealthOrgansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); //禁用外键约束
        DB::table('health_organs')->truncate();

        DB::transaction(function () {
            $result = DB::connection('mysql_tvbs_v4')->table('health_organs')->get();
            $result->transform(function($i) {
                if (!empty($i->icon)) {
                    $i->icon = UrlHelper::parseUrl($i->icon);
                }
                if (!empty($i->icon_1)) {
                    $i->icon_1 = UrlHelper::parseUrl($i->icon_1);
                }
                if (!empty($i->icon_2)) {
                    $i->icon_2 = UrlHelper::parseUrl($i->icon_2);
                }
                return (array)$i;
            });
            $result = $result->toArray();
            foreach($result as $k => $v){

                if($result[$k]['updated_time'] == '0000-00-00 00:00:00'){
                    $result[$k]['updated_time'] = null;
                }

                $result[$k]['icon_android'] = $result[$k]['icon_1'];
                $result[$k]['icon_ios'] = $result[$k]['icon_2'];
                $result[$k]['created_at'] = $result[$k]['created_time'];
                $result[$k]['updated_at'] = $result[$k]['updated_time'];
                unset($result[$k]['icon_1'],$result[$k]['icon_2'],$result[$k]['created_time'],$result[$k]['updated_time']);
            }
            OrgansModel::insert($result);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); //啟用外键约束
    }
}
