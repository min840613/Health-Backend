<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleApiBlock;
use App\Http\Requests\ArticleApiHot;
use App\Http\Requests\ArticleApiNews;
use App\Http\Requests\ArticleApiRightBlock;
use App\Http\Resources\ArticleApiBlockCollection;
use App\Http\Resources\ArticleApiBlockMetaResource;
use App\Http\Resources\ArticleApiHotCollection;
use App\Http\Resources\ArticleApiNewsCollection;
use App\Http\Resources\ArticleApiRightBlockCollection;
use App\Services\ArticlesService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ArticleApiController
 * @package App\Http\Controllers\Api
 */
class ArticleApiController extends Controller
{
    /** @var int 本日最新取得筆數 */
    public const NEWS_COUNT = 6;

    /** @var int 熱門文章取得筆數 */
    public const HOT_COUNT = 10;

    /** @var int 浮動區塊取得筆數 */
    public const BLOCK_COUNT = 6;

    /** @var int 右側浮動區塊取得筆數 */
    public const RIGHT_BLOCK_COUNT = 1;

    public $master_type = [
        0 => '健康2.0影音',
        1 => '名醫觀點',
        2 => '食譜'
    ];

    /** @var ArticlesService */
    private ArticlesService $service;

    /**
     * ArticleApiController constructor.
     * @param ArticlesService $service
     */
    public function __construct(ArticlesService $service)
    {
        $this->service = $service;
    }

    /**
     * 本日最新
     * @param ArticleApiNews $request
     * @return Response
     */
    public function news(ArticleApiNews $request): Response
    {
        $data = $request->validated();

        $count = $data['count'] ?? self::NEWS_COUNT;

        $expectArticleIds = explode(',', $data['except_article_ids'] ?? '');

        $articles = $this->service->newsArticle($count, $expectArticleIds);

        return response()->success(ArticleApiNewsCollection::collection($articles));
    }

    /**
     * 熱門文章
     * @param ArticleApiHot $request
     * @return Response
     */
    public function hot(ArticleApiHot $request): Response
    {
        $data = $request->validated();

        $count = $data['count'] ?? self::HOT_COUNT;

        $expectArticleIds = explode(',', $data['except_article_ids'] ?? '');

        $articles = $this->service->hotArticle($count, $expectArticleIds);

        return response()->success(ArticleApiHotCollection::collection($articles));
    }

    /**
     * 浮動區塊
     * @param int $blockNum
     * @param ArticleApiBlock $request
     * @return Response
     */
    public function block(int $blockNum, ArticleApiBlock $request): Response
    {
        $data = $request->validated();

        $count = $data['count'] ?? self::BLOCK_COUNT;

        $expectArticleIds = explode(',', $data['except_article_ids'] ?? '');

        $blockArticles = $this->service->blockArticle($blockNum, $count, $expectArticleIds);

        if ($blockArticles->isEmpty()) {
            return response()->success([]);
        }

        return response()->success(
            ArticleApiBlockCollection::collection($blockArticles['articles']),
            ArticleApiBlockMetaResource::make($blockArticles)
        );
    }

    /**
     * 右側浮動區塊
     * @param int $blockNum
     * @param ArticleApiRightBlock $request
     * @return Response
     */
    public function right_block(int $blockNum, ArticleApiRightBlock $request): Response
    {
        $data = $request->validated();

        $count = $data['count'] ?? self::RIGHT_BLOCK_COUNT;

        $rightBlockArticles = $this->service->rightBlockArticle($blockNum, $count);

        $meta = [];
        if($rightBlockArticles->first()){
            $meta['name'] = $rightBlockArticles['rightBar']->name;
            $meta['type'] = $this->master_type[$rightBlockArticles['rightBar']->article_require_master];
            $meta['main_category'] = $rightBlockArticles['mainCategory']->name;
            $meta['main_category_en'] = $rightBlockArticles['mainCategory']->en_name;
            $meta['sub_category_id'] = empty($rightBlockArticles['subCategory']) ? null : $rightBlockArticles['subCategory']->sub_categories_id;
            $meta['sub_category'] = empty($rightBlockArticles['subCategory']) ? null : $rightBlockArticles['subCategory']->name;
            $meta['sub_category_en'] = empty($rightBlockArticles['subCategory']) ? null : $rightBlockArticles['subCategory']->en_name;
        }

        return response()->success(ArticleApiRightBlockCollection::collection($rightBlockArticles['article'] ?? []), $meta);
    }
}
