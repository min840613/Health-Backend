<?php

namespace App\Events;

use App\Models\Articles\ArticleModel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ArticleUpdated
 * @package App\Events
 */
class ArticleUpdated implements ArticleSave
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var ArticleModel */
    private ArticleModel $article;

    /**
     * Create a new event instance.
     *
     * @param ArticleModel $article
     */
    public function __construct(ArticleModel $article)
    {
        $this->article = $article;
    }

    /**
     * @return ArticleModel
     */
    public function getArticle(): ArticleModel
    {
        return $this->article;
    }
}
