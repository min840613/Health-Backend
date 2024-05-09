<?php

namespace App\Services;

use App\Models\Categories\MainCategoriesModel;
use App\Models\Categories\SubCategoriesModel;
use App\Repositories\ArticlesRepository;
use App\Repositories\MainCategoriesRepository;
use App\Repositories\SponsorAdRepository;
use App\Repositories\SubCategoriesRepository;
use Illuminate\Support\Collection;

/**
 * Class CategoryService
 * @package App\Services
 */
class CategoryService
{
    /** @var MainCategoriesRepository */
    private MainCategoriesRepository $repository;

    /** @var ArticlesRepository */
    private ArticlesRepository $articlesRepository;

    /** @var SponsorAdRepository */
    private SponsorAdRepository $sponsorAdRepository;

    /** @var ArticlesService */
    private ArticlesService $articleService;

    /** @var SubCategoriesRepository  */
    private SubCategoriesRepository $subCategoriesRepository;

    /**
     * MenuListService constructor.
     * @param MainCategoriesRepository $repository
     * @param SubCategoriesRepository $subCategoriesRepository
     * @param ArticlesRepository $articlesRepository
     * @param SponsorAdRepository $sponsorAdRepository
     * @param ArticlesService $articlesService
     */
    public function __construct(
        MainCategoriesRepository $repository,
        SubCategoriesRepository $subCategoriesRepository,
        ArticlesRepository $articlesRepository,
        SponsorAdRepository $sponsorAdRepository,
        ArticlesService $articlesService
    ) {
        $this->repository = $repository;
        $this->subCategoriesRepository = $subCategoriesRepository;
        $this->articlesRepository = $articlesRepository;
        $this->sponsorAdRepository = $sponsorAdRepository;
        $this->articleService = $articlesService;
    }

    /**
     * 關於分頁又有塞廣編稿的問題，需於取得文章及判斷是否為廣編稿範圍內做補正，否則會有文章遺失
     *  - 假設 page = 2, sponsor: 3, 8
     *  - 共有 1~10 號文章
     *  - 在取得 page 2 時需有一筆為廣編稿，page 5 時有另一筆廣編稿
     * @param array $data
     * @param int $count
     * @return Collection
     */
    public function list(array $data, int $count): Collection
    {
        $page = request()->input('page') ?? 1;

        $originCount = $count;

        $sponsors = $this->sponsorAdRepository->getActiveSponsorWithCategories($data['main_category_en'], $data['sub_category_en'] ?? null);

        $originSponsors = clone $sponsors;

        $exceptSponsorArticlesId = $originSponsors->pluck('article_id')->toArray();

        $offsetCorrection = $originSponsors->where('position', '<', $page * $count)->count();

        if ($sponsors->isNotEmpty() && $inRange = $this->articleService->isInRange($count, $page, $sponsors->pluck('position')->toArray(), $offsetCorrection)) {
            $sponsors = $sponsors->whereIn('position', $inRange)->keyBy('position')->sortBy('position');
            $count -= $sponsors->count();
        } else {
            $sponsors = collect([]);
        }

        $articles = $this->articlesRepository->findByCategories($data['main_category_en'], $data['sub_category_en'] ?? null, $count, $offsetCorrection, $exceptSponsorArticlesId);

        foreach ($sponsors as $index => $sponsor) {
            if ($index % $originCount > $articles->count()) {
                continue;
            }

            $sponsor->article->setRelation('mainCategory', $sponsor->article->mainCategories->where('sort', 0)->first());
            $articles->splice($index - 1, 0, [$sponsor->article]);
        }

        return $articles;
    }

    /**
     * @param string $enName
     * @return MainCategoriesModel|null
     */
    public function getByMainCategoryEn(string $enName): ?MainCategoriesModel
    {
        return $this->repository->getByMainCategoryEn($enName);
    }

    /**
     * @param string|null $enName
     * @return MainCategoriesModel|null
     */
    public function getBySubCategoryEn(?string $enName): ?SubCategoriesModel
    {
        return $enName === null ? null : $this->subCategoriesRepository->getByEnName($enName);
    }
}
