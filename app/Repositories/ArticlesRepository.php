<?php

namespace App\Repositories;

use App\Enums\MainCategoriesType;
use App\Models\Articles\ArticleModel;
use App\Models\DailyViewCountModel;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class ArticlesRepository
 * @package App\Repositories
 */
class ArticlesRepository
{
    /** @var ArticleModel */
    private ArticleModel $model;

    /**
     * ArticlesRepository constructor.
     * @param ArticleModel $model
     */
    public function __construct(ArticleModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $data
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function find(array $data, int $perPage): LengthAwarePaginator
    {
        return $this->model->with(['mainCategories' => function ($query) {
            $query->orderBy('sort');
        }, 'subCategories'])
            ->when(!empty($data['type']), function ($query) use ($data) {
                if ($data['type'] == 'ad') {
                    $query->whereHas('mainCategories', function ($query) use ($data) {
                        $query->where('categories_type', '3');
                    });
                } else {
                    $query->whereDoesntHave('mainCategories', function ($query) use ($data) {
                        $query->where('categories_type', '3');
                    });
                }
            })
            ->when(!empty($data['search_publish_start']), function ($query) use ($data) {
                $query->where('publish', '>=', $data['search_publish_start']);
            })
            ->when(!empty($data['search_publish_end']), function ($query) use ($data) {
                $query->where('publish', '<=', Carbon::parse($data['search_publish_end'])->endOfDay());
            })
            ->when(!empty($data['search_main_category_id']) && $data['search_main_category_id'] != -1, function ($query) use ($data) {
                $query->whereHas('mainCategories', function ($query) use ($data) {
                    $query->where('categories_id', $data['search_main_category_id']);
                });
            })
            ->when(!empty($data['search_sub_category_id']) && $data['search_sub_category_id'] != -1, function ($query) use ($data) {
                $query->whereHas('subCategories', function ($query) use ($data) {
                    $query->where('sub_categories_id', $data['search_sub_category_id']);
                });
            })
            ->when(isset($data['search_articles_status']) && $data['search_articles_status'] != -1, function ($query) use ($data) {
                $query->where('articles_status', $data['search_articles_status']);
            })
            ->when(!empty($data['search_author']) && $data['search_author'] != -1, function ($query) use ($data) {
                $query->where('author', $data['search_author']);
            })
            ->when(!empty($data['search_keyword']), function ($query) use ($data) {
                foreach ($data['search_keyword'] as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->orWhere('title', 'like', "%{$keyword}%");
                        $query->orWhere('tag', 'like', "%{$keyword}%");
                        $query->orWhere('article_content', 'like', "%{$keyword}%");
                        // $query->WhereNot(function ($query) use ($keyword) {
                        //     $query->where('article_content', 'REGEXP', "看更多：<a[^>]*>(?!.*{$keyword}).*<\/a>");
                        //     $query->orWhere('article_content', 'REGEXP', "延伸閱讀：<a[^>]*>(?!.*{$keyword}).*<\/a>");
                        // });
                        // todo: 確認功能後實作
                        // health_articles_match_search_data
                        // $query->orWhereIn('articles_id', );
                    });
                }
            })
            ->when(!empty($data['search_articles_id']), function ($query) use ($data) {
                $query->where('articles_id', $data['search_articles_id']);
            })
            ->orderByDesc('publish')
            ->paginate($perPage)
            ->appends($data);
    }

    /**
     * @param int $count
     * @param array $expectArticleIds
     * @param int|null $mainCategory
     * @return LengthAwarePaginator
     */
    public function hotArticle(int $count, array $expectArticleIds = [], array $mainCategory = []): LengthAwarePaginator
    {
        return $this->model::with(['viewCount', 'mainCategory', 'subCategories'])
            ->addSelect([
                'viewCountClick' => DailyViewCountModel::selectRaw('sum(click_count) as total')->whereColumn('source_id', 'health_articles.articles_id')->groupBy('source_id')
            ])
            ->whereHas('viewCount', function ($query) {
                $query->where('date', '>=', now()->subDays(3));
            })
            ->whereHas('mainCategory', function ($query) use ($mainCategory) {
                $query->when($mainCategory, function ($query, $mainCategory) {
                    $query->whereIn('categories_id', $mainCategory);
                });
                $query->whereNot('categories_type', MainCategoriesType::ADVERTORIAL);
            })
            ->active()
            ->when(!empty($expectArticleIds), function ($query) use ($expectArticleIds) {
                $query->whereNotIn('articles_id', $expectArticleIds);
            })
            ->orderByDesc('viewCountClick')
            ->groupBy('articles_id')
            ->paginate($count);
    }

    /**
     * 補齊文章
     * @param int $count
     * @param array $exceptIds
     * @param int|null $main_category
     * @param int $sub_category
     * @param int $master_type
     * @return Collection
     */
    public function additional(int $count, array $exceptIds, ?int $main_category = null, int $sub_category = 0, int $master_type = 0): Collection
    {
        return $this->model::with(['mainCategories', 'masters'])
            ->when(!empty($master_type), function ($query) use ($master_type) {
                $query->whereHas('masters', function ($query) use ($master_type) {
                    $query->where('type', $master_type);
                });
            })
            ->when(!empty($sub_category) && !empty($main_category), function ($query) use ($sub_category, $main_category) {
                $query->whereHas('subCategories', function ($query) use ($sub_category, $main_category) {
                    $query->where('category_id', $sub_category)
                        ->where('parent', $main_category) //是否要加條件為主分類才出現
                        ->groupBy('article_id');
                });
            })
            ->whereHas('mainCategories', function ($query) use ($main_category, $sub_category) {
                $query->when($main_category && empty($sub_category), function ($query) use ($main_category) {
                    $query->where('categories_id', $main_category);
                });
                $query->where('sort', 0)->whereNot('categories_type', MainCategoriesType::ADVERTORIAL);
            })
            ->whereNotIn('articles_id', $exceptIds)
            ->active()
            ->orderBy('publish', 'desc')
            ->limit($count)
            ->get();
    }

    /**
     * @param string $mainCategoryEn
     * @param string|null $subCategoryEn
     * @param int $count
     * @param int $offsetCorrection
     * @return Collection
     */
    public function findByCategories(string $mainCategoryEn, ?string $subCategoryEn, int $count, int $offsetCorrection = 0, array $exceptSponsorArticlesId = []): Collection
    {
        return $this->model::with(['mainCategories'])
            ->whereHas('mainCategories', function ($query) use ($mainCategoryEn) {
                $query->active()->filterAdvertorial()->where('en_name', $mainCategoryEn);
            })
            ->when($subCategoryEn, function ($query, $subCategoryEn) {
                $query->whereHas('subCategories', function ($query) use ($subCategoryEn) {
                    $query->where('en_name', $subCategoryEn);
                });
            })
            ->when(!empty($exceptSponsorArticlesId), function ($query) use ($exceptSponsorArticlesId) {
                $query->whereNotIn('articles_id', $exceptSponsorArticlesId);
            })
            ->active()
            ->offset((((request()->page ?? 1) - 1) * $count) - $offsetCorrection)
            ->limit($count)
            ->orderByDesc('publish')
            ->orderByDesc('updated_at')
            ->get();
    }
}
