<?php

namespace App\Listeners;

use App\Events\ArticleSave;
use App\Events\ArticleStored;
use App\Events\ArticleUpdated;
use App\Models\Articles\ArticlesSicknessModel;
use Illuminate\Support\Collection;

/**
 * Class SyncSicknessArticles
 * @package App\Listeners
 */
class SyncSicknessArticles
{
    /**
     * Handle the event.
     * @param ArticleSave $event
     * @return void
     * @throws \Throwable
     */
    public function handle(ArticleSave $event): void
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $article = $event->getArticle();

        $article->load(['ArticlesSicknessMapping']);

        if ($event instanceof ArticleStored) {
            if ($article->ArticlesSicknessMapping->isNotEmpty()) {
                \DB::connection('mysql_tvbs_old')->table('health_articles_sickness')->insert(
                    $this->parseAtToTime($article->ArticlesSicknessMapping)->toArray()
                );
            }
        }

        if ($event instanceof ArticleUpdated) {
            \DB::connection('mysql_tvbs_old')->transaction(function () use ($article) {
                \DB::connection('mysql_tvbs_old')->table('health_articles_sickness')->where('article_id', $article->articles_id)->delete();

                if ($article->ArticlesSicknessMapping->isNotEmpty()) {
                    \DB::connection('mysql_tvbs_old')->table('health_articles_sickness')->insert(
                        $this->parseAtToTime($article->ArticlesSicknessMapping)->toArray()
                    );
                }
            });
        }
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    private function parseAtToTime(Collection $collection): Collection
    {
        return $collection->map(function (ArticlesSicknessModel $mapping) {
            $mapping->created_time = $mapping->created_at;
            $mapping->updated_time = $mapping->updated_at;

            unset($mapping['created_at'], $mapping['updated_at']);

            return $mapping;
        });
    }
}
