<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryApiIndex;
use App\Http\Resources\ArticleApiHotCollection;
use App\Http\Resources\CategoryApiIndexMetaResource;
use App\Http\Resources\Home\CategoryApiNavResource;
use App\Services\CategoryService;
use App\Services\MenuListService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryApiController
 * @package App\Http\Controllers\Api
 */
class CategoryApiController extends Controller
{
    /** @var int 導覽列熱門文章筆數 */
    public const NAV_HOT_ARTICLE_COUNT = 4;

    /** @var int 分類列表筆數 */
    public const CATEGORY_LIST_COUNT = 10;

    /** @var CategoryService */
    private CategoryService $service;

    /** @var MenuListService */
    private MenuListService $menuListService;

    /**
     * CategoryApiController constructor.
     * @param CategoryService $service
     * @param MenuListService $menuListService
     */
    public function __construct(CategoryService $service, MenuListService $menuListService)
    {
        $this->service = $service;
        $this->menuListService = $menuListService;
    }

    /**
     * @param CategoryApiIndex $request
     * @return Response
     */
    public function index(CategoryApiIndex $request): Response
    {
        $data = $request->validated();

        $articles = $this->service->list($data, $data['count'] ?? self::CATEGORY_LIST_COUNT);

        $mainCategory = $this->service->getByMainCategoryEn($data['main_category_en']);
        $subCategory = $this->service->getBySubCategoryEn($data['sub_category_en'] ?? null);

        return response()->success(
            ArticleApiHotCollection::collection($articles),
            CategoryApiIndexMetaResource::make(!empty($data['sub_category_en']) ? $subCategory : $mainCategory)
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function nav(Request $request): Response
    {
        $menuList = $this->menuListService->nav(self::NAV_HOT_ARTICLE_COUNT);

        $request->merge(['type' => 'nav']);

        return response()->success(CategoryApiNavResource::collection($menuList));
    }
}
