<?php

namespace App\Events;

use App\Models\Articles\ArticleModel;

/**
 * Class ArticleSave
 * @package App\Events
 */
interface ArticleSave
{
    /**
     * @return ArticleModel
     */
    public function getArticle(): ArticleModel;
}
