<?php

namespace Database\Seeders;

use App\Helpers\UrlHelper;
use App\Models\Articles\ArticleModel;
use App\Models\Articles\ArticlesFurtherReadingModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HealthArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $result = DB::connection('mysql_tvbs_v4')->table('health_articles')->get();
            $result->transform(function ($column) {
                // 測試站為 nullable 但正式站為 not null，故在這作轉換
                if ($column->match_searchs === null) {
                    $column->match_searchs = '';
                }
                if (!empty($column->image)) {
                    $column->image = UrlHelper::parseUrl($column->image);
                }
                if (!empty($column->ogimage)) {
                    $column->ogimage = UrlHelper::parseUrl($column->ogimage);
                }
                if (!empty($column->fb_ia_video)) {
                    $column->fb_ia_video = \Storage::disk('s3_old')->url($column->fb_ia_video);
                }
                if (!empty($column->article_content)) {
                    $column->article_content = UrlHelper::parseUrl($column->article_content);
                }
                $column->yahoo_ext = explode(',', $column->yahoo_ext);
                return (array)$column;
            });
            $result = $result->toArray();
            foreach ($result as $v) {
                if ($v['updated_time'] == '0000-00-00 00:00:00') {
                    $v['updated_time'] = null;
                }

                $v['created_at'] = $v['created_time'];
                $v['updated_at'] = $v['updated_time'];

                $furtherReadingResult = [];
                foreach ($v['yahoo_ext'] as $recommendation) {
                    if (empty($recommendation)) {
                        continue;
                    }
                    $furtherReadingResult[] = [
                        'article_id' => $v['articles_id'],
                        'recommendation_article_id' => $recommendation,
                        'type' => 'yahoo',
                        'created_user' => $v['created_user'],
                        'updated_user' => $v['updated_user'],
                    ];
                }
                ArticlesFurtherReadingModel::insert($furtherReadingResult);

                unset($v['product_id'], $v['activity_id'], $v['description'], $v['attractions'], $v['external_source'], $v['external_link']);
                unset($v['created_time'], $v['updated_time'], $v['categories_main'], $v['categories_id'], $v['sub_categories_id'], $v['yahoo_ext']);

                ArticleModel::insert($v);
            }
        });
    }
}
