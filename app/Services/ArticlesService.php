<?php

namespace App\Services;

use App\Enums\SponsorAdCategoriesList;
use App\Models\Authors\AuthorsModel;
use App\Models\Categories\MainCategoriesModel;
use App\Repositories\ArticlesRepository;
use App\Repositories\KeyvisualRepository;
use App\Repositories\SponsorAdRepository;
use App\Repositories\TaxonRepository;
use App\Repositories\RightBarRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class ArticlesService
 * @package App\Services
 */
class ArticlesService
{
    /** @var int */
    private $perPage = 30;

    /** @var ArticlesRepository */
    private ArticlesRepository $repository;

    /** @var TaxonRepository */
    private TaxonRepository $taxonRepository;

    /** @var RightBarRepository */
    private RightBarRepository $rightBarRepository;

    /** @var KeyvisualRepository */
    private KeyvisualRepository $keyvisualRepository;

    /** @var SponsorAdRepository */
    private SponsorAdRepository $sponsorAdRepository;

    /**
     * ArticlesService constructor.
     * @param ArticlesRepository $repository
     * @param TaxonRepository $taxonRepository
     * @param RightBarRepository $rightBarRepository
     * @param KeyvisualRepository $keyvisualRepository
     * @param SponsorAdRepository $sponsorAdRepository
     */
    public function __construct(
        ArticlesRepository $repository,
        TaxonRepository $taxonRepository,
        RightBarRepository $rightBarRepository,
        KeyvisualRepository $keyvisualRepository,
        SponsorAdRepository $sponsorAdRepository
    ) {
        $this->repository = $repository;
        $this->taxonRepository = $taxonRepository;
        $this->rightBarRepository = $rightBarRepository;
        $this->keyvisualRepository = $keyvisualRepository;
        $this->sponsorAdRepository = $sponsorAdRepository;
    }

    /**
     * @return array
     */
    public function filters(): array
    {
        return [
            'main_categories' => MainCategoriesModel::with(['subCategories'])->active()->get()->toArray(),
            'publish_status' => [-1, 1, 0],
            'authors' => AuthorsModel::query()->active()->get()->toArray(),
        ];
    }

    /**
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function find(array $data): LengthAwarePaginator
    {
        if (empty($data['search_publish_start'])) {
            $data['search_publish_start'] = '1911-01-01';
            request()->merge(['search_publish_start' => $data['search_publish_start']]);
        }

        if (empty($data['search_publish_end'])) {
            $data['search_publish_end'] = Carbon::now()->addYears(10)->toDateString();
            request()->merge(['search_publish_end' => $data['search_publish_end']]);
        }

        if (!empty($data['search_keyword'])) {
            $data['search_keyword'] = str_replace('　', ' ', $data['search_keyword']); //全行空白轉半形
            $data['search_keyword'] = preg_replace("/\s(?=\s)/", "\\1", $data['search_keyword']); //將多餘的空白變成只有一個
            $data['search_keyword'] = str_replace([' ', '+', ';'], ',', $data['search_keyword']);
            $data['search_keyword'] = explode(',', $data['search_keyword']);
        }

        return $this->repository->find($data, $this->perPage);
    }

    /**
     * @param int $count
     * @param array $expectArticleIds
     * @return Collection
     */
    public function newsArticle(int $count, array $expectArticleIds): Collection
    {
        $keyVisuals = $this->keyvisualRepository->newsArticle($count, $expectArticleIds);

        $additionalArticles = collect([]);
        if ($keyVisuals->count() < $count) {
            $additionalCount = $count - $keyVisuals->count();

            $additionalArticles = $this->repository->additional(
                $additionalCount,
                $keyVisuals->pluck('source_id')->merge($expectArticleIds)->filter()->toArray()
            );
        }

        return $keyVisuals->merge($additionalArticles);
    }

    /**
     * @param int $count
     * @param int $page
     * @param array $positions
     * @param int $offsetCorrection
     * @return array
     */
    public function isInRange(int $count, int $page, array $positions, int $offsetCorrection = 0): array
    {
        $range = ['min' => ($count * ($page - 1)) - $offsetCorrection, 'max' => ($count * $page) - $offsetCorrection];

        $allows = [];
        foreach ($positions as $position) {
            if ($position > $range['min'] && $position <= $range['max']) {
                $allows[] = $position;
            }
        }

        return $allows;
    }

    /**
     * @param int $count
     * @param array $expectArticleIds
     * @return LengthAwarePaginator
     */
    public function hotArticle(int $count, array $expectArticleIds = [], array $mainCategory = []): LengthAwarePaginator
    {
        $sponsors = $this->sponsorAdRepository->getActiveSponsor(SponsorAdCategoriesList::HOME);

        $count = $count - $sponsors->count();

        if ($sponsors->isNotEmpty() && $this->isInRange($count, request()->input('page') ?? 1, $sponsors->pluck('position')->toArray())) {
            $sponsors = $sponsors->keyBy('position');
        } else {
            $sponsors = [];
        }

        $articles = $this->repository->hotArticle($count, $expectArticleIds, $mainCategory);

        foreach ($sponsors as $index => $sponsor) {
            $sponsor->article->setRelation('mainCategory', $sponsor->article->mainCategories->first());
            $articles->splice($index - 1, 0, [$sponsor->article]);
        }

        return $articles;
    }

    /**
     * @param int $count
     * @param array $expectArticleIds
     * @return LengthAwarePaginator
     */
    public function getFurtherData(int $count, int $further_count, array $expectArticleIds = [], array $mainCategory = []): array
    {
        $hotArticle_array = $this->repository->hotArticle($count, $expectArticleIds, $mainCategory)->toArray();
        $articles = $hotArticle_array['data'];
        $num = min(count($articles), 6);
        $random_indexes = $articles ? array_rand($articles, $num) : 0;
        $random_articles = is_array($random_indexes) ? array_intersect_key($articles, array_flip($random_indexes)) : $articles;

        $pluck_articles = [];
        if (count($random_articles) < $further_count) :
            $pluck_articles = $this->repository->hotArticle(20, $expectArticleIds)->toArray();
            $pluck_num = min(count($pluck_articles['data']), ($further_count - count($random_articles)));
            $pluck_random_indexes = $pluck_articles['data'] ? array_rand($pluck_articles['data'], $pluck_num) : 0;
            $pluck_random_articles = is_array($pluck_random_indexes) ? array_intersect_key($pluck_articles['data'], array_flip($pluck_random_indexes)) : ($pluck_random_indexes >= 0 ? $pluck_articles['data'][$pluck_random_indexes] : $pluck_articles['data']);

            foreach ($pluck_random_articles as $v) :
                $random_articles[] = $v;
            endforeach;
        endif;

        return $random_articles;
    }

    /**
     * @param int $blockNum
     * @param int $count
     * @param array $expectArticleIds
     * @return Collection
     */
    public function blockArticle(int $blockNum, int $count, array $expectArticleIds): Collection
    {
        $taxon = $this->taxonRepository->block($blockNum, $count, $expectArticleIds);

        if ($taxon === null) {
            return collect([]);
        }

        if ($taxon->article !== null) {
            if (!now()->between($taxon->published_at, $taxon->published_end)) {
                $taxon->article = null;
            } else {
                $taxon->categoryArticles = $taxon->categoryArticles->whereNotIn('articles_id', [$taxon->article->articles_id]);
            }
        }

        if ($taxon->categoryArticles !== null && $taxon->categoryArticles->count() < $count) {
            $additionalCount = $count - $taxon->categoryArticles->count();

            $additionalArticles = $this->repository->additional(
                $additionalCount,
                $taxon->categoryArticles->pluck('articles_id')->merge($expectArticleIds)->merge([$taxon->article->articles_id ?? ''])->filter()->toArray(),
                $taxon->categories_id
            );

            $taxon->categoryArticles = $taxon->categoryArticles->merge($additionalArticles);
        }

        return collect(['taxon' => $taxon, 'articles' => $taxon->categoryArticles, 'headline' => $taxon->article]);
    }

    public function rightBlockArticle(int $blockNum, int $count)
    {
        $rightBar = $this->rightBarRepository->right_block($blockNum, $count);

        if (empty($rightBar)) {
            return collect([]);
        }

        $article = [];
        $exceptArticleId = [];

        $article = collect([]);
        foreach ($rightBar->detail as $val) {
            if ($val->article) {
                $val->article->title = $val->name;
                $article = $article->push($val->article);
                $exceptArticleId[] = $val->article->articles_id;
            }
        }

        $additionalArticles = collect([]);

        if (count($article) < $count) {
            $additionalCount = $count - count($article);

            $additionalArticles = $this->repository->additional(
                $additionalCount,
                $exceptArticleId,
                $rightBar->main_category,
                $rightBar->sub_category,
                $rightBar->article_require_master
            );

            $additionalArticles->each(function ($item) use ($article) {
                $article = $article->push($item);
            });
        }

        return collect(['rightBar' => $rightBar->withoutRelations(),
            'detail' => $rightBar->detail,
            'article' => empty($article) ? null : $article,
            'mainCategory' => $rightBar->mainCategory,
            'subCategory' => $rightBar->subCategory,
        ]);
    }
}
