<?php

namespace App\Repositories;

use App\Enums\MainCategoriesType;
use App\Models\Categories\MainCategoriesModel;
use App\Models\DailyViewCountModel;
use Illuminate\Support\Collection;

/**
 * Class MainCategoriesRepository
 * @package App\Repositories
 */
class MainCategoriesRepository
{
    /** @var MainCategoriesModel */
    private MainCategoriesModel $model;

    /**
     * MainCategoriesRepository constructor.
     * @param MainCategoriesModel $model
     */
    public function __construct(MainCategoriesModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $mainCategories
     * @param int $count
     * @return Collection
     */
    public function withHotArticles(array $mainCategories, int $count):  Collection
    {
        return MainCategoriesModel::with(['articles' => function ($query) use ($count) {
            $query->with(['mainCategory', 'subCategories', 'tags'])
                ->addSelect(['viewCountClick' => DailyViewCountModel::selectRaw('sum(click_count) as total')->whereColumn('source_id', 'health_articles.articles_id')->groupBy('source_id')])
                ->whereHas('viewCount', function ($query) {
                    $query->where('date', '>=', now()->subDays(3));
                })
                ->active()
                ->orderByDesc('viewCountClick')
                ->limit(10);
        }])
            ->whereIn('categories_id', $mainCategories)
            ->whereNot('categories_type', MainCategoriesType::ADVERTORIAL)
            ->get();
    }

    /**
     * @param string $enName
     * @return MainCategoriesModel|null
     */
    public function getByMainCategoryEn(string $enName): ?MainCategoriesModel
    {
        return $this->model::where('en_name', $enName)->first();
    }
}
