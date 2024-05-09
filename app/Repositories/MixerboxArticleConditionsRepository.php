<?php

namespace App\Repositories;

use App\Models\ThirdPartyFeed\MixerboxArticleConditionModel;
use Illuminate\Support\Collection;

/**
 * Class MixerboxArticleConditionsRepository
 * @package App\Repositories
 */
class MixerboxArticleConditionsRepository
{
    /** @var MixerboxArticleConditionModel */
    private MixerboxArticleConditionModel $model;

    /**
     * todo: 目前沒有表，先用 line 的
     * MixerboxArticleConditionsRepository constructor.
     * @param MixerboxArticleConditionModel $model
     */
    public function __construct(MixerboxArticleConditionModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model::all();
    }

    /**
     * @param array $categoriesId
     * @return Collection
     */
    public function withoutCategoriesId(array $categoriesId): Collection
    {
        return $this->model::query()->whereNotIn('category_id', $categoriesId)->get();
    }
}
