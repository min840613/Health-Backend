<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\App\AppVersionsModel;
use Illuminate\Support\Facades\DB;


class HealthAppVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $result = DB::connection('mysql_tvbs_old')->table('health_app_version')->get();
            $result->transform(function ($column) {
                return (array)$column;
            });
            $result = $result->toArray();

            foreach ($result as $r) {
                $versions = [];
                if ($r['update'] == '0000-00-00 00:00:00') {
                    $r['update'] = null;
                }
                $versions[] = [
                    'device' => $r['device'],
                    'version' => $r['version'],
                    'limit_version' => $r['limit_version'],
                    'release_note' => $r['release_note'],
                    'created_user' => 'dev001',
                    'updated_user' => 'dev001',
                    'created_at' => $r['created'],
                    'updated_at' => $r['update']
                ];

                AppVersionsModel::insert($versions);
            }
        });
    }
}
