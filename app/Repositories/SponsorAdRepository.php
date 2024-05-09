<?php

namespace App\Repositories;

use App\Enums\SponsorAdCategoriesType;
use App\Models\Articles\SponsorAdModel;
use Illuminate\Support\Collection;

/**
 * Class SponsorAdRepository
 * @package App\Repositories
 */
class SponsorAdRepository
{
    /** @var SponsorAdModel */
    private SponsorAdModel $model;

    /**
     * SponsorAdRepository constructor.
     * @param SponsorAdModel $model
     */
    public function __construct(SponsorAdModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $listId
     * @return Collection
     */
    public function getActiveSponsor(int $listId): Collection
    {
        return $this->model::with(['article.mainCategories' => function ($query) {
            $query->filterAdvertorial();
        }, 'article.subCategories'])
            ->where('categories_list_id', $listId)
            ->active()
            ->get();
    }

    /**
     * @param string $mainCategoryEn
     * @param string|null $subCategoryEn
     * @return Collection
     */
    public function getActiveSponsorWithCategories(string $mainCategoryEn, ?string $subCategoryEn): Collection
    {
        return $this->model::with(['mainCategory', 'article.subCategories'])
            ->when($subCategoryEn, function ($query, $subCategoryEn) {
                $query->subCategoryType();
                $query->whereHas('SubCategories', function ($query) use ($subCategoryEn) {
                    $query->where('en_name', $subCategoryEn);
                });
            })
            ->when($subCategoryEn === null, function ($query) use ($mainCategoryEn) {
                $query->whereHas('mainCategory', function ($query) use ($mainCategoryEn) {
                    $query->filterAdvertorial()->where('en_name', $mainCategoryEn);
                })->mainCategoryType();
            })
            ->active()
            ->get();
    }
}
