<?php

namespace Database\Seeders;

use App\Models\Articles\ArticleModel;
use App\Models\Articles\ArticleTagMappingModel;
use Illuminate\Database\Seeder;

class HealthArticleTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();

        $articles = ArticleModel::query()->select(['articles_id', 'tag'])->get();

        $queries = [];

        $articles->each(function ($article) use (&$queries, $now) {
            $tags = explode(',', $article->tag);
            $tags = array_filter($tags);

            foreach ($tags as $tag) {
                $queries[] = [
                    'article_id' => $article->articles_id,
                    'tag' => trim($tag),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        });

        // PDO支持最大佔位符為65535
        $chunks = array_chunk($queries, 10000);

        \DB::transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                ArticleTagMappingModel::insert($chunk);
            }
        });
    }
}
