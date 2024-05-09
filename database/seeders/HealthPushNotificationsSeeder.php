<?php

namespace Database\Seeders;

use App\Helpers\UrlHelper;
use App\Models\App\NotificationsModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class HealthPushNotificationsSeeder
 * @package Database\Seeders
 */
class HealthPushNotificationsSeeder extends Seeder
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
            $result = DB::connection('mysql_tvbs_v4')->table('health_push_notifications')->get();
            $result->transform(function ($column) {
                if (!empty($column->image)) {
                    $column->image = UrlHelper::parseUrl($column->image);
                }
                $column->id = $column->push_notifications_id;
                return (array)$column;
            });
            $result = $result->toArray();

            foreach ($result as $k => $v) {
                if ($result[$k]['updated_time'] == '0000-00-00 00:00:00') {
                    $result[$k]['updated_time'] = null;
                }

                $result[$k]['created_at'] = $result[$k]['created_time'];
                $result[$k]['updated_at'] = $result[$k]['updated_time'];
                unset($result[$k]['created_time'], $result[$k]['updated_time'], $result[$k]['push_notifications_id']);
            }

            $chunks = array_chunk($result, 2000);

            foreach ($chunks as $chunk) {
                NotificationsModel::insert($chunk);
            }

        });
    }
}
