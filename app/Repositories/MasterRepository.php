<?php

namespace App\Repositories;

use App\Enums\MainCategoriesType;
use App\Models\Masters\MastersModel;
use App\Models\Masters\MastersBannerModel;
use App\Models\Masters\MasterExpertiseModel;
use App\Models\Masters\DivisionsModel;
use App\Models\Masters\InstitutionsModel;
use App\Models\Articles\ArticleModel;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class MasterRepository
 * @package App\Repositories
 */
class MasterRepository
{
    /** @var MastersModel */
    private MastersModel $model;

    /** @var MastersBannerModel */
    private MastersBannerModel $MastersBannerModel;

    /** @var MasterExpertiseModel */
    private MasterExpertiseModel $MasterExpertiseModel;

    /** @var DivisionsModel */
    private DivisionsModel $DivisionsModel;

    /** @var InstitutionsModel */
    private InstitutionsModel $InstitutionsModel;

    /** @var ArticleModel */
    private ArticleModel $ArticleModel;

    /**
     * MasterRepository constructor.
     * @param MastersModel $model
     * @param MastersBannerModel $MastersBannerModel
     * @param MasterExpertiseModel $MasterExpertiseModel
     * @param DivisionsModel $DivisionsModel
     * @param InstitutionsModel $InstitutionsModel
     * @param ArticleModel $ArticleModel
     */
    public function __construct(
        MastersModel $model,
        MastersBannerModel $MastersBannerModel,
        MasterExpertiseModel $MasterExpertiseModel,
        DivisionsModel $DivisionsModel,
        InstitutionsModel $InstitutionsModel,
        ArticleModel $ArticleModel
    ) {
        $this->model = $model;
        $this->MastersBannerModel = $MastersBannerModel;
        $this->MasterExpertiseModel = $MasterExpertiseModel;
        $this->DivisionsModel = $DivisionsModel;
        $this->InstitutionsModel = $InstitutionsModel;
        $this->ArticleModel = $ArticleModel;
    }

    /**
     * @param string $expertEnName
     * @return MastersModel|null
     */
    public function getByEnName(string $expertEnName): ?MastersModel
    {
        return $this->model::with(['institution' => function ($query) {
            $query->active();
        }, 'divisions.division' => function ($query) {
            $query->active();
        }, 'experiences', 'expertise', 'articles' => function ($query) {
            $query->active()->orderByDesc('publish');
            $query->whereHas('mainCategory', function ($query) {
                $query->whereNot('categories_type', MainCategoriesType::ADVERTORIAL);
            });
        }])->where('en_name', $expertEnName)->active()->first();
    }

    /**
     * @param int $count
     * @return Illuminate\Database\Eloquent\Collection|MastersBannerModel[]
     */
    public function getBanners($count)
    {
        return $this->MastersBannerModel::active()
                    ->orderBy('sort', 'asc')
                    ->orderBy('id', 'desc')
                    ->with(['master', 'institution', 'division'])
                    ->limit($count)
                    ->get();
    }

    /**
     * @param int $count
     * @return Array
     */
    public function getExpertiseKeywordRandom($count)
    {
        return $this->MasterExpertiseModel::inRandomOrder()
                    ->take($count)
                    ->get()
                    ->pluck('name')
                    ->toArray();
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getDivisionsWithMasters()
    {
        return $this->DivisionsModel::active()
            ->where('type', '!=', 2)
            ->with(['masters' => function ($query) {
                $query->active();
            }])
            ->orderBy('sort', 'asc')
            ->get();
    }

    /**
     * @param int $divition_id 若為0則為全部
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getInstitutionsWithMasterCountByDivisionId($divition_id = 0)
    {
        $condition = !empty($divition_id) ? "AND hms.division_id = " . $divition_id : "";
        $query = "
                    SELECT hi.*, a.master_count
                    FROM health_institutions hi
                    JOIN (
                        SELECT hm.institution_id, COUNT(hm.id) as master_count
                        FROM health_masters hm
                        JOIN health_master_divisions hms ON hm.id = hms.master_id
                                " . $condition . "
                        JOIN health_divisions hd ON hms.division_id = hd.id AND hd.status = 1 AND hd.type != 2
                        WHERE hm.status = 1
                        GROUP BY hm.institution_id
                    ) a ON a.institution_id = hi.id
                    WHERE hi.status = 1
                    GROUP BY hi.id
                    ORDER BY hi.sort ASC";

        return collect(DB::select($query));
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection|DivisionsModel[]
     */
    public function getDivisions()
    {
        return $this->DivisionsModel::active()->where('type', '!=', 2)->get();
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection|InstitutionsModel[]
     */
    public function getInstitutionsIsCentre()
    {
        return $this->InstitutionsModel::active()->isCentre()->get();
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection|InstitutionsModel[]
     */
    public function getInstitutions()
    {
        return $this->InstitutionsModel::active()->get();
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection|MastersModel[]
     */
    public function getMasters()
    {
        return $this->model::active()->isDoctor()->get();
    }

    /**
     * @param array $search
     * @return Illuminate\Database\Eloquent\Collection|MastersModel[]
     */
    public function getExpertsByConditions($search)
    {
        return $this->model::active()
                    ->isDoctor()
                    ->addSelect([
                        'latest_article_date' => ArticleModel::select('publish')->whereColumn('id', 'health_articles.talent_category_id')->orderByDesc('publish')->limit(1)
                    ])
                    ->when(!empty($search['division']), function ($q) use ($search) {
                        $q->whereHas('divisions.division', function ($q) use ($search) {
                                $q->where('en_name', $search['division'])
                                    ->active();
                        });
                    })
                    ->when(!empty($search['institution']), function ($q) use ($search) {
                        $q->whereHas('institution', function ($q) use ($search) {
                                $q->where('en_name', $search['institution'])
                                    ->active();
                        });
                    })
                    ->when(!empty($search['expertise_keyword']), function ($q) use ($search) {
                        $q->where(function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search['expertise_keyword'] . '%')
                                ->orWhereHas('expertise', function ($query) use ($search) {
                                    $query->where('name', 'like', '%' . $search['expertise_keyword'] . '%');
                                });
                        });
                    })
                    ->orderByDesc('latest_article_date')
                    ->orderBy('id')
                    ->with('divisions.division')
                    ->with('institution')
                    ->with('expertise')
                    ->withCount(['articles' => function ($query) {
                        $query->active();
                    }])
                    ->paginate($search['count']);
    }

    /**
     * @param array $count
     * @return LengthAwarePaginator
     */
    public function getNewArticles($count): LengthAwarePaginator
    {
        return $this->ArticleModel::whereHas('masters', function ($q) {
                                        $q->isDoctor()
                                            ->active();
        })
                                    ->with(['mainCategory', 'subCategories', 'tags', 'masters.divisions.division'])
                                    ->active()
                                    ->orderByDesc('publish')
                                    ->paginate($count);
    }
}
