<?php

namespace Database\Seeders;

use App\Models\App\ShakeModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HealthShakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Throwable
     */
    public function run()
    {
        DB::transaction(function () {
            $result = DB::connection('mysql_tvbs_v4')->table('health_shake')->get();
            $result->transform(function ($column) {
                return (array)$column;
            });
            $result = $result->toArray();

            foreach ($result as $k => $v) {
                if ($result[$k]['updated_time'] == '0000-00-00 00:00:00') {
                    $result[$k]['updated_time'] = null;
                }

                $result[$k]['created_at'] = $result[$k]['created_time'];
                $result[$k]['updated_at'] = $result[$k]['updated_time'];
                unset($result[$k]['created_time'], $result[$k]['updated_time']);
            }
            ShakeModel::insert($result);
        });
    }
}
