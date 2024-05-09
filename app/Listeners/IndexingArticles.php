<?php

namespace App\Listeners;

use App\Events\ArticleSave;
use App\Events\ArticleStored;
use App\Events\ArticleUpdated;
use App\Exceptions\GoogleIndexingException;
use App\Helpers\UrlHelper;
use App\Models\Articles\ArticleModel;

/**
 * Class IndexingArticles
 * @package App\Listeners
 */
class IndexingArticles
{
    /**
     * Handle the event.
     * @param ArticleSave $event
     * @return void
     * @throws \Throwable
     */
    public function handle(ArticleSave $event): void
    {
        $article = $event->getArticle();

        switch (true) {
            case $event instanceof ArticleStored:
                $this->create($article);
                break;
            case $event instanceof ArticleUpdated:
                $this->update($article);
                break;
        }
    }

    /**
     * @param ArticleModel $article
     * @return bool
     */
    private function isPublished(ArticleModel $article): bool
    {
        return $article->articles_status === 1 && now()->greaterThanOrEqualTo($article->publish);
    }

    /**
     * @param ArticleModel $article
     * @return string
     */
    private function generateWebUrl(ArticleModel $article): string
    {
        return UrlHelper::generateWebUrl($article->articles_id, $article->mainCategory->en_name);
    }

    /**
     * @param ArticleModel $article
     * @return void
     */
    private function create(ArticleModel $article): void
    {
        try {
            if ($this->isPublished($article)) {
                \Indexing::create($this->generateWebUrl($article));
            }
        } catch (GoogleIndexingException $exception) {
            \Log::alert($exception);
        }
    }

    /**
     * @param ArticleModel $article
     * @return void
     */
    private function update(ArticleModel $article): void
    {
        $changes = $article->getChanges();

        try {
            if (!empty($changes) && isset($changes['articles_status'])) {
                if ($changes['articles_status'] == 0) {
                    \Indexing::delete($this->generateWebUrl($article));
                } elseif ($changes['articles_status'] == 1 && now()->greaterThanOrEqualTo($article->publish)) {
                    \Indexing::update($this->generateWebUrl($article));
                }
                return;
            }

            if (!empty($changes) && $this->isPublished($article)) {
                \Indexing::update($this->generateWebUrl($article));
            }
        } catch (GoogleIndexingException $exception) {
            \Log::alert($exception);
        }
    }
}
