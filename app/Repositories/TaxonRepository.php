<?php

namespace App\Repositories;

use App\Models\HomeArea\HomeTaxonModel;
use Illuminate\Support\Collection;

/**
 * Class TaxonRepository
 * @package App\Repositories
 */
class TaxonRepository
{
    /** @var HomeTaxonModel */
    private HomeTaxonModel $model;

    /**
     * TaxonRepository constructor.
     * @param HomeTaxonModel $model
     */
    public function __construct(HomeTaxonModel $model)
    {
        $this->model = $model;
    }

    /**
     * 當該篇文章無該分類時，依舊取出該置頂文章
     * @param int $blockNum
     * @param int $count
     * @param array $expectArticleIds
     * @return HomeTaxonModel|null
     */
    public function block(int $blockNum, int $count, array $expectArticleIds): ?HomeTaxonModel
    {
        return $this->model::with(['categoryArticles' => function ($query) use ($count, $expectArticleIds) {
            $query->whereNotIn('articles_id', $expectArticleIds)->active()->limit($count)->latest();
        }, 'article' => function ($query) {
            $query->active();
        }, 'mainCategory' => function($query){
            $query->active();
        }])
            ->active()
            ->orderBy('sort')
            ->offset($blockNum)
            ->first();
    }
}
