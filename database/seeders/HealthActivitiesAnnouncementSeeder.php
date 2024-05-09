<?php

namespace Database\Seeders;

use App\Helpers\UrlHelper;
use App\Models\App\ActivitiesAnnouncementModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class HealthActivitiesAnnouncementSeeder
 * @package Database\Seeders
 */
class HealthActivitiesAnnouncementSeeder extends Seeder
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
            $result = DB::connection('mysql_tvbs_v4')->table('health_activities_announcement')->get();
            $result->transform(function ($column) {
                if (!empty($column->image_url)) {
                    $column->image_url = UrlHelper::parseUrl($column->image_url);
                }
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
            ActivitiesAnnouncementModel::insert($result);
        });
    }
}
