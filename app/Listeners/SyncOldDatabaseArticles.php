<?php

namespace App\Listeners;

use App\Enums\DailyViewCountPlatform;
use App\Events\ArticleStored;

/**
 * Class SyncOldDatabaseArticles
 * @package App\Listeners
 */
class SyncOldDatabaseArticles
{
    /**
     * Handle the event.
     *  - 舊資料表有欄位，但新的沒有：attractions, description, activity_id, product_id, is_zimedia, external_source, external_link
     *  - 有欄位但因舊文章沒實作，所以不同步：talent_category_id
     * @param ArticleStored $event
     * @return void
     * @throws \Throwable
     */
    public function handle(ArticleStored $event): void
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $article = $event->getArticle();

        $article->load(['mainCategories', 'subCategories', 'recommendations', 'yahooRecommendations', 'tags']);

        $further = [];

        $articleData = $article->only([
            'articles_id',
            'articles_status',
            'publish',
            'title',
            'og_title',
            'seo_title',
            'author',
            'author_type',
            'medicine_article_category_id',
            'adult_flag',
            'image',
            'image_alt',
            'ogimage',
            'video_id',
            'video_type',
            'fb_ia_video',
            'match_searchs',
            'article_content',
            'is_line_rss',
            'video_file_name',
            'is_yahoo_rss',
            'created_user',
            'updated_user',
            'match_url',
            'collaborator',
        ]);

        $articleData['description'] = $articleData['activity_id'] = $articleData['product_id'] = $articleData['external_source'] = $articleData['external_link'] = '';

        /**
         * 'updated_time', // rename
         * 'created_time', // rename
         * 'tag', // tags
         * 'extended_article', // recommendations
         * 'yahoo_ext', // yahooRecommendations
         * 'categories_main', //main_category
         * 'categories_id', // main_categories
         * 'sub_categories_id', // sub_categories
         */
        $categoriesId = $article->mainCategories()->orderBy('sort')->get()->pluck('categories_id');

        $articleData['video_type'] = 'youtube';
        $articleData['created_time'] = $article->created_at;
        $articleData['updated_time'] = $article->updated_at;
        $articleData['tag'] = $article->tags->pluck('tag')->implode(',') ?? '';
        $articleData['image_alt'] = $articleData['image_alt'] ?? '';
        $articleData['extended_article'] = '';
        $articleData['yahoo_ext'] = $article->yahooRecommendations->pluck('articles_id')->implode(',') ?? '';
        $articleData['categories_main'] = $article->mainCategory->categories_id;
        $articleData['categories_id'] = $categoriesId->isEmpty() ? ',' : ',' . $categoriesId->implode(',') . ',';

        // 判斷子分類的資料(只抓影音)
        $articleData['sub_categories_id'] = optional($article->subCategories()->where('parent', 25)->orderBy('sort')->first())->sub_categories_id;

        if ($article->recommendations->isNotEmpty()) {
            $further = [
                'article_id' => $article->articles_id,
                'recommendation_list' => $article->recommendations->pluck('articles_id')->implode(','),
                'created_time' => $article->created_at,
                'created_user' => $article->created_user,
                'updated_time' => $article->updated_at,
                'updated_user' => $article->updated_user,
            ];
            $articleData['extended_article'] = $article->recommendations->pluck('articles_id')->implode(',');
        }

        \DB::connection('mysql_tvbs_old')->transaction(function () use ($articleData, $further, $article) {
            \DB::connection('mysql_tvbs_old')->table('health_articles')->insert($articleData);
            if (!empty($further)) {
                \DB::connection('mysql_tvbs_old')->table('health_articles_further_reading')->insert($further);
            }
            \DB::connection('mysql_tvbs_old')->table('daily_views_count')->insert([
                'prg_id' => DailyViewCountPlatform::HEALTH,
                'date' => now()->toDateString(),
                'source_type' => $article->mainCategory->en_name,
                'source_id' => $article->articles_id,
                'click_count' => 1,
            ]);
        });
    }
}
