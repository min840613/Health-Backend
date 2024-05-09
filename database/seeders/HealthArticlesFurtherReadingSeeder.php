<?php

namespace Database\Seeders;

use App\Models\Articles\ArticlesFurtherReadingModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HealthArticlesFurtherReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $result = DB::connection('mysql_tvbs_v4')->table('health_articles_further_reading')->get();
            $result->transform(function ($column) {
                $column->recommendation_list = explode(',', $column->recommendation_list);
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

                if (empty($result[$k]['article_id'])) {
                    continue;
                }
                $furtherReadingResult = [];
                foreach ($result[$k]['recommendation_list'] as $recommendation) {
                    $furtherReadingResult[] =[
                        'article_id' => $result[$k]['article_id'],
                        'recommendation_article_id' => $recommendation,
                        'type' => 'article',
                        'created_user' => $result[$k]['created_user'],
                        'updated_user' => $result[$k]['updated_user'],
                    ];
                }
                ArticlesFurtherReadingModel::insert($furtherReadingResult);
            }
        });
    }
}
