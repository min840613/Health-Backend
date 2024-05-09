<?php

namespace App\Services;

use App\Models\Categories\MenuListModel;
use App\Repositories\ArticlesRepository;
use App\Repositories\MainCategoriesRepository;
use App\Repositories\MenuListRepository;
use Illuminate\Support\Collection;

/**
 * Class MenuListService
 * @package App\Services
 */
class MenuListService
{
    /** @var MenuListRepository */
    private MenuListRepository $repository;

    /** @var ArticlesRepository */
    private ArticlesRepository $articlesRepository;

    /** @var MainCategoriesRepository */
    private MainCategoriesRepository $mainCategoriesRepository;

    /**
     * MenuListService constructor.
     * @param MenuListRepository $repository
     * @param ArticlesRepository $articlesRepository
     * @param MainCategoriesRepository $mainCategoriesRepository
     */
    public function __construct(
        MenuListRepository $repository,
        ArticlesRepository $articlesRepository,
        MainCategoriesRepository $mainCategoriesRepository
    )
    {
        $this->repository = $repository;
        $this->articlesRepository = $articlesRepository;
        $this->mainCategoriesRepository = $mainCategoriesRepository;
    }

    /**
     * @param int $count
     * @return Collection
     */
    public function nav(int $count): Collection
    {
        $nav = $this->repository->nav();

        $mainCategories = $this->mainCategoriesRepository->withHotArticles($nav->pluck('categories_id')->toArray(), $count);

        $mainCategories = $mainCategories->map(function ($model) use ($count) {
            if ($model->articles->isNotEmpty()) {
                $count = $model->articles->count() < $count ? $model->articles->count() : $count;
                $model->articles = $model->articles->random($count);
            }
            return $model;
        })->keyBy('categories_id');

        $nav->transform(function (MenuListModel $model) use ($mainCategories, $count) {
            $hotArticles = $mainCategories[$model->categories_id]->articles ?? collect([]);

            $model->setRelation('articles', $hotArticles);

            if ($hotArticles->count() < $count) {
                $model = $this->appendArticles($count, $hotArticles, $model);
            }
            // 如果有關聯子分類, 就針對子分類進行排序
            if ($model->subCategories->count() > 0) {
                $model->subCategories = $model->subCategories->sortBy('sort');
            }
            
            return $model;
        });

        return $nav;
    }

    /**
     * @param int $count
     * @param Collection $hotArticles
     * @param MenuListModel $model
     * @return MenuListModel
     */
    private function appendArticles(int $count, Collection $hotArticles, MenuListModel $model): MenuListModel
    {
        $additionalCount = $count - $hotArticles->count();

        $additional = $this->articlesRepository->additional(
            $additionalCount,
            $hotArticles->pluck('articles_id')->toArray(),
            $model->categories_id
        );

        $model->articles = $model->articles->merge($additional);

        return $model;
    }
}
