<?php

namespace App\Repositories;

use App\Models\Articles\KeyvisualModel;
use Illuminate\Support\Collection;

/**
 * Class KeyvisualRepository
 * @package App\Repositories
 */
class KeyvisualRepository
{
    /** @var KeyvisualModel */
    private KeyvisualModel $model;

    /**
     * KeyvisualRepository constructor.
     * @param KeyvisualModel $model
     */
    public function __construct(KeyvisualModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $count
     * @param array $expectArticleIds
     * @return Collection
     */
    public function newsArticle(int $count, array $expectArticleIds): Collection
    {
        return $this->model::with(['article' => function ($query) use ($expectArticleIds) {
            $query->whereNotIn('articles_id', $expectArticleIds);
        }, 'article.mainCategory'])
            ->active()
            ->orderByDesc('sort')
            ->limit($count)
            ->get();
    }
}
