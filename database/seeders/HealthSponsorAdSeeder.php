<?php

namespace Database\Seeders;

use App\Models\Articles\SponsorAdModel;
use Illuminate\Database\Seeder;

/**
 * Class HealthSponsorAdSeeder
 * @package Database\Seeders
 */
class HealthSponsorAdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::transaction(function () {
            $result = \DB::connection('mysql_tvbs_v4')->table('health_sponsor_ad')->get();
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
                $result[$k]['start'] = $result[$k]['start_time'];
                $result[$k]['end'] = $result[$k]['end_time'];
                unset($result[$k]['created_time'], $result[$k]['updated_time'], $result[$k]['start_time'],$result[$k]['end_time']);
            }

            SponsorAdModel::insert($result);
        });
    }
}
