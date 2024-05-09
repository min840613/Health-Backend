<?php

namespace Database\Seeders;

use App\Enums\DailyViewCountPlatform;
use App\Models\DailyViewCountOldModel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HealthDailyViewsCountOldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $result = DB::connection('mysql_tvbs_v4')
                ->table('daily_views_count')
                ->where('prg_id', DailyViewCountPlatform::HEALTH)
                ->where('date', '>=', '2023-01-01')
                ->where('date', '<=', Carbon::now()->subDay(3)->format('Y-m-d'))
                ->get();

            $result->transform(function ($i) {
                return (array)$i;
            });
            $result = $result->toArray();
            foreach ($result as $k => $v) {
                unset($result[$k]['prg_id']);
            }

            $chunks = array_chunk($result, 2000);

            foreach ($chunks as $chunk) {
                DailyViewCountOldModel::insert($chunk);
            }
        });
    }
}
