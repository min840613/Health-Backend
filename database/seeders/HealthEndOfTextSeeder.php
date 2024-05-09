<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Articles\EndTextModel;

class HealthEndOfTextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $result = DB::connection('mysql_tvbs_v4')->table('health_end_of_text')->get();
            $result->transform(function($i) {
                return (array)$i;
            });
            $result = $result->toArray();
            foreach($result as $k => $v){

                if($result[$k]['updata_time'] == '0000-00-00 00:00:00'){
                    $result[$k]['updata_time'] = null;
                }

                $result[$k]['content'] = $result[$k]['content'];

                $result[$k]['created_at'] = $result[$k]['ins_time'];
                $result[$k]['updated_at'] = $result[$k]['updata_time'];
                $result[$k]['created_user'] = $result[$k]['update_user'];
                $result[$k]['updated_user'] = $result[$k]['update_user'];
                unset($result[$k]['ins_time'], $result[$k]['updata_time'], $result[$k]['update_user']);
            }
            EndTextModel::insert($result);
        });
    }
}
