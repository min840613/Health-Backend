<?php

namespace Database\Seeders;

use App\Models\Articles\ArticleCategoriesMappingsModel;
use App\Models\Categories\MainCategoriesModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HealthArticleCategoriesMappingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {

            $videoCategory = MainCategoriesModel::where('name', '影音')->first();

            $articles = DB::connection('mysql_tvbs_v4')->table('health_articles')->get();

            $queries = [];

            $articles->each(function ($article) use (&$queries, $videoCategory) {
                $this->parseMainCategories($article->articles_id, $article->categories_id, $queries);
                $this->parseSubCategories($article->articles_id, $article->sub_categories_id, $queries, $videoCategory->categories_id);
            });

            foreach ($queries as $query) {
                ArticleCategoriesMappingsModel::insert($query);
            }
        });
    }

    /**
     * @param int $articleId
     * @param string $categoryId
     * @param array $queries
     */
    private function parseMainCategories(int $articleId, string $categoryId, array &$queries)
    {
        $categories = explode(',', $categoryId);
        $categories = array_filter($categories);
        $categories = array_values($categories);

        if (!empty($categories)) {
            foreach ($categories as $index => $category) {
                $queries[] = [
                    'article_id' => $articleId,
                    'category_id' => $category,
                    'sort' => $index,
                    'parent' => null,
                ];
            }
        }
    }

    /**
     * @param int $articleId
     * @param string|null $categoryId
     * @param array $queries
     * @param int $videoCategory
     */
    private function parseSubCategories(int $articleId, ?string $categoryId, array &$queries, int $videoCategory)
    {
        if (!empty($categoryId)) {
            $queries[] = [
                'article_id' => $articleId,
                'category_id' => $categoryId,
                'sort' => 0,
                'parent' => $videoCategory,
            ];
        }
    }
}
