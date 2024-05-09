<?php

namespace Database\Seeders;

use App\Helpers\UrlHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\App\AppActivitiesModel;

class HealthAppActivitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $result = DB::connection('mysql_tvbs_v4')->table('health_app_activities')->get();
            $result->transform(function($i) {
                if (!empty($i->link)) {
                    $i->link = UrlHelper::parseUrl($i->link);
                }
                return (array)$i;
            });
            $result = $result->toArray();
            foreach($result as $k => $v){

                if($result[$k]['released'] == '0000-00-00 00:00:00'){
                    $result[$k]['released'] = date('Y-m-d H:i:s');
                }
                if($result[$k]['start'] == '0000-00-00 00:00:00'){
                    $result[$k]['start'] = date('Y-m-d H:i:s');
                }
                if($result[$k]['end'] == '0000-00-00 00:00:00'){
                    $result[$k]['end'] = date('Y-m-d H:i:s');
                }
                if($result[$k]['updated_time'] == '0000-00-00 00:00:00'){
                    $result[$k]['updated_time'] = null;
                }
                if($result[$k]['updated_user'] == null){
                    $result[$k]['updated_user'] = $result[$k]['created_user'];
                }

                $result[$k]['created_at'] = $result[$k]['created_time'];
                $result[$k]['updated_at'] = $result[$k]['updated_time'];
                unset($result[$k]['created_time'],$result[$k]['updated_time']);
            }
            AppActivitiesModel::insert($result);
        });
    }
}
