<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Encyclopedia\BodyModel;

class HealthBodySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $result = DB::connection('mysql_tvbs_v4')->table('health_body')->get();
            $result->transform(function($i) {
                return (array)$i;
            });
            $result = $result->toArray();
            foreach($result as $k => $v){

                if($result[$k]['updated_time'] == '0000-00-00 00:00:00'){
                    $result[$k]['updated_time'] = null;
                }

                $result[$k]['created_at'] = $result[$k]['created_time'];
                $result[$k]['updated_at'] = $result[$k]['updated_time'];
                unset($result[$k]['created_time'],$result[$k]['updated_time']);
            }
            BodyModel::insert($result);
        });
    }
}
