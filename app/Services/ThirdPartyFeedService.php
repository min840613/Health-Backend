<?php

namespace App\Services;

use App\Repositories\MixerboxArticleConditionsRepository;
use App\Repositories\MixerboxArticlesRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class ThirdPartyFeedService
 * @package App\Services
 */
class ThirdPartyFeedService
{
    /** @var MixerboxArticlesRepository */
    private MixerboxArticlesRepository $mixerboxArticlesRepository;

    /** @var MixerboxArticleConditionsRepository */
    private MixerboxArticleConditionsRepository $mixerboxArticleConditionsRepository;

    /**
     * @param MixerboxArticlesRepository $mixerboxArticlesRepository
     * @param MixerboxArticleConditionsRepository $mixerboxArticleConditionsRepository
     */
    public function __construct(
        MixerboxArticlesRepository $mixerboxArticlesRepository,
        MixerboxArticleConditionsRepository $mixerboxArticleConditionsRepository,
    ) {
        $this->mixerboxArticlesRepository = $mixerboxArticlesRepository;
        $this->mixerboxArticleConditionsRepository = $mixerboxArticleConditionsRepository;
    }

    /**
     * @param Carbon $date
     * @param int $total
     * @param int $categoryPerLimit
     * @return Collection
     */
    public function mixerboxRss(Carbon $date, int $total, int $categoryPerLimit): Collection
    {
        $mixerboxArticles = $this->mixerboxArticlesRepository->rss($date);

        $releasedArticles = $mixerboxArticles->whereNotNull('release_date');

        if ($releasedArticles->countBy('article.mainCategory.categories_id')->sum() < $total) {
            $unusedArticles = $mixerboxArticles->whereNull('release_date')->sortBy('created_at');

            $allowCategories = $this->mixerboxArticleConditionsRepository->all();

            $mixerboxArticles = $this->filterByCategoriesAndConditions($unusedArticles, $releasedArticles, $allowCategories, $categoryPerLimit, $total);

            $this->mixerboxArticlesRepository->release($mixerboxArticles);

            $mixerboxArticles = $mixerboxArticles->merge($releasedArticles);
        } else {
            $mixerboxArticles = $releasedArticles;
        }

        return $mixerboxArticles->sortByDesc('created_at')->pluck('article');
    }

    /**
     * @param Collection $unusedArticles
     * @param Collection $releasedArticles
     * @param Collection $allowCategories
     * @param int $categoryPerLimit
     * @param int $total
     * @return Collection
     */
    private function filterByCategoriesAndConditions(Collection $unusedArticles, Collection $releasedArticles, Collection $allowCategories, int $categoryPerLimit, int $total): Collection
    {
        $countByCategory = $releasedArticles->pluck('article.mainCategory.categories_id');

        return $unusedArticles->filter(function ($item) use ($allowCategories) {
            return $allowCategories->pluck('category_id')->contains($item->article->mainCategory->categories_id);
        })->map(function ($item) use ($categoryPerLimit, &$countByCategory, $total) {
            $categoryId = $item->article->mainCategory->categories_id;

            if (collect($countByCategory)->countBy()->get($categoryId, 0) >= $categoryPerLimit || count($countByCategory) >= $total) {
                return [];
            }

            $countByCategory[] = $item->article->mainCategory->categories_id;

            return $item;
        })->filter();
    }
}
