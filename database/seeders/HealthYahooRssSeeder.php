<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ThirdPartyFeed\YahooRssModel;

class HealthYahooRssSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $result = DB::connection('mysql_tvbs_rss')->table('external_rss_yahoo')
                ->where([
                    ['program_id', '=', 'health'],
                    ['status', '=', 1]
                ])
                ->where(function ($query) {
                    $query->whereNull('rss_release_date')
                          ->orWhere('rss_release_date', '=', date('Y-m-d'));
                })
                ->get();
            $result->transform(function ($column) {
                return (array)$column;
            });
            $result = $result->toArray();

            foreach ($result as $r) {
                $record = [];

                $record[] = [
                    'article_id' => $r['news_id'],
                    'status' => 1,
                    'rss_release_date' => $r['rss_release_date'] ? $r['rss_release_date'] : NULL,
                    'created_user' => $r['record_editor'],
                    'created_at' => $r['record_create_time']
                ];

                YahooRssModel::insert($record);
            }
        });
    }
}
