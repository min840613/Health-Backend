<?php

namespace App\Repositories;

use App\Models\Categories\SubCategoriesModel;
use Illuminate\Support\Collection;


class SubCategoriesRepository
{
    private SubCategoriesModel $model;

    public function __construct(SubCategoriesModel $model)
    {
        $this->model = $model;
    }

    /**
     * 取得子分類底下的文章
     * @param int $sub_category_id
     * @return SubCategoriesModel|null
     */
    public function getArticlesCount(int $sub_category_id): ?SubCategoriesModel
    {
        return $this->model::withCount(['articles'])
            ->where('sub_categories_id', $sub_category_id)
            ->get()->first();
    }

    /**
     * @param string $enName
     * @return SubCategoriesModel|null
     */
    public function getByEnName(string $enName): ?SubCategoriesModel
    {
        return $this->model::where('en_name', $enName)->first();
    }
}
