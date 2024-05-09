<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpertsApi;
use App\Http\Requests\ExpertsApiNewArticles;
use App\Http\Resources\MasterShowResource;
use App\Http\Resources\MasterBannerResource;
use App\Http\Resources\MasterConditionsResource;
use App\Http\Resources\MasterExpertsResource;
use App\Http\Resources\MasterNewArticlesResource;
use App\Services\MasterService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MasterApiController
 * @package App\Http\Controllers\Api
 */
class MasterApiController extends Controller
{
    /** @var int 取得Banner數量 */
    public const BANNER_COUNT = 6;

    /** @var int 取得專長Keyword數量 */
    public const EXPERTISE_KEYWORD_COUNT = 10;

    /** @var int 取得醫師數量 */
    public const EXPERTS_COUNT = 9;

    /** @var int 取得最新文章數量 */
    public const NEW_ARTICLES_COUNT = 6;

    /** @var MasterService */
    private MasterService $service;

    /**
     * MasterApiController constructor.
     * @param MasterService $service
     */
    public function __construct(MasterService $service)
    {
        $this->service = $service;
    }

    /**
     * @param string $expertEnName
     * @return Response
     */
    public function show(string $expertEnName): Response
    {
        $master = $this->service->show($expertEnName);

        return response()->success($master === null ? [] : MasterShowResource::make($master));
    }

    /**
     * @return Response
     */
    public function banner(): Response
    {
        $banner = $this->service->banner(self::BANNER_COUNT);

        $meta['division_count'] = $this->service->divisions()->count();
        $meta['institution_count'] = $this->service->institutions(true)->count();
        $meta['expert_count'] = $this->service->masters()->count();

        return response()->success(MasterBannerResource::collection($banner), $meta);
    }

     /**
     * @return Response
     */
    public function conditions(): Response
    {
        $conditions = $this->service->conditions();
        $meta['expertise_keyword'] = $this->service->expertiseKeywordRandom(self::EXPERTISE_KEYWORD_COUNT);

        return response()->success(MasterConditionsResource::collection($conditions), $meta);
    }

    /**
     * @param ExpertsApi $request
     * @return Response
     */
    public function experts(ExpertsApi $request): Response
    {
        $data = $request->validated();

        $search['count'] = $data['count'] ?? self::EXPERTS_COUNT;
        $search['division'] = $data['division'] ?? null;
        if (!empty($search['division'])) {
            $divisions = $this->service->divisions()->pluck('name', 'en_name')->toArray();
            if (!array_key_exists($search['division'], $divisions)) {
                $search['division'] = null;
            }
        }
        $search['institution'] = $data['institution'] ?? null;
        $search['expertise_keyword'] = $data['expertise_keyword'] ?? null;

        $experts = $this->service->experts($search);
        $meta['total_page'] = $experts->lastPage();
        return response()->success(MasterExpertsResource::collection($experts), $meta);
    }

    /**
     * @param ExpertsApiNewArticles $request
     * @return Response
     */
    public function newArticles(ExpertsApiNewArticles $request): Response
    {
        $data = $request->validated();

        $count = $data['count'] ?? self::NEW_ARTICLES_COUNT;

        $newArticles = $this->service->newArticles($count);

        return response()->success(MasterNewArticlesResource::collection($newArticles));
    }
}
