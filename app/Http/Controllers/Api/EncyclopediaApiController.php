<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\EncyclopediaApiMostFocus;
use App\Services\EncyclopediaService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Encyclopedia\MostFocussicknessApiCollection;

class EncyclopediaApiController extends Controller
{

    /** @var int 最多關注疾病 */
    public const SICKNESS_COUNT = 8;

    /** @var int 取得對應疾病文章 */
    public const ARTICLE_COUNT = 6;

    /** @var EncyclopediaService */
    private EncyclopediaService $service;

    /**
     * ArticleApiController constructor.
     * @param EncyclopediaService $service
     */
    public function __construct(EncyclopediaService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mostFocusSickness(EncyclopediaApiMostFocus $request): Response
    {
        $data = $request->validated();

        $sickness_count = $data['sickness_count'] ?? self::SICKNESS_COUNT;
        $article_count = $data['article_count'] ?? self::ARTICLE_COUNT;
        // $sickness_id = $data['sickness_id'] ?? NULL;

        $sicknessArticles = $this->service->mostFocusSickness($sickness_count, $article_count);
        $meta['main_category'] = $sicknessArticles->first()->articles->first()->mainCategory->name ?? '';
        $meta['main_category_en'] = $sicknessArticles->first()->articles->first()->mainCategory->en_name ?? '';
        // dd($sicknessArticles);
        return response()->success(MostFocussicknessApiCollection::collection($sicknessArticles ?? []), $meta);
    }
}
