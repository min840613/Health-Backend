<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MixerboxArticlesCollection;
use App\Services\ThirdPartyFeedService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Carbon;
use App\Http\Resources\YahooRssCollection;
use App\Http\Resources\LineArticlesCollection;
use App\Models\ThirdPartyFeed\YahooRssModel;
use App\Models\ThirdPartyFeed\LineArticlesModel;

class ThirdPartyFeedController extends Controller
{
    /** @var int 每日Line文章總共稿數 */
    public const LINE_ARTICLES_TOTAL = 6;

    /** @var int 每日Line文章供稿每個分類限制筆數 */
    public const LINE_ARTICLES_LIMIT = 2;

    /** @var int 每日yahoo供稿限制筆數 */
    public const YAHOO_ARTICLES_LIMIT = 7;

    /** @var int 每日Mixerbox文章總共稿數 */
    public const MIXERBOX_ARTICLES_TOTAL = 6;

    /** @var int 每日Mixerbox文章供稿每個分類限制筆數 */
    public const MIXERBOX_ARTICLES_LIMIT = 2;

    /** @var ThirdPartyFeedService  */
    private ThirdPartyFeedService $service;

    /**
     * @param ThirdPartyFeedService $service
     */
    public function __construct(ThirdPartyFeedService $service)
    {
        $this->service = $service;
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function yahoo(Request $request): Response
    {
        $today = Carbon::today()->toDateString();

        $info = [
            'site_name' => '健康2.0',
            'site_url' => config('constants.frontend_url')
        ];

        $request->merge(['info' => $info]);

        $articles = YahooRssModel::with(['article.mainCategory', 'article.yahooRecommendations', 'furtherReading.furtherArticle.mainCategory', 'furtherReading.furtherArticleTitle'])
            ->whereHas('article', function ($q) {
                $q->active();
            })
            ->whereNull('rss_release_date')
            ->orWhere('rss_release_date', '=', $today)
            ->orderBy('id', 'asc')
            ->limit(self::YAHOO_ARTICLES_LIMIT)
            ->get();

        foreach ($articles as $article) {
            if (!$article->rss_release_date) {
                YahooRssModel::find($article->id)->update(['rss_release_date' => $today]);
            }
        }

        return response()->xml('third_party_feed.yahoo.yahoo', [
            'site_name' => $info['site_name'],
            'domain' => $info['site_url'],
            'description' => $info['site_name'],
            'time' => Carbon::now()->format('D, d M Y 08:00:00 +0800'),
            'articles' => YahooRssCollection::collection($articles)->toArray($request),
        ]);
    }

    public function lineArticles()
    {
        $datas = LineArticlesModel::whereHas('article', function ($query) {
                            $query->active()
                                    ->where('is_line_article', 1);
        })
                        ->where('status', 1)
                        ->whereHas('article', function ($query) {
                            $query->active();
                        })
                        ->whereHas('article.mainCategory', function ($query) {
                                $query->filterAdvertorial();
                        })
                        ->with('article.mainCategory')
                        ->with('article.recommendations.mainCategory')
                        ->whereNotNull('release_date')
                        ->where('release_date', Carbon::now()->format('Y-m-d'))
                        ->orderBy('created_at', 'DESC')
                        ->get();

        $add_num = self::LINE_ARTICLES_TOTAL - $datas->count();

        if ($add_num > 0) {
            $categoriesLimit = [];
            foreach ($datas as $v) {
                if (array_key_exists($v->article->mainCategory->categories_id, $categoriesLimit)) {
                    $categoriesLimit[$v->article->mainCategory->categories_id] ++;
                } else {
                    $categoriesLimit[$v->article->mainCategory->categories_id] = 1;
                }
            }

            $LINE_ARTICLES_LIMIT = self::LINE_ARTICLES_LIMIT;
            $matchCategoriesLimitKey = array_keys(array_filter($categoriesLimit, function ($value) use ($LINE_ARTICLES_LIMIT) {
                return $value >= $LINE_ARTICLES_LIMIT;
            }));

            $searchDatas = LineArticlesModel::whereHas('article', function ($query) {
                                        $query->active()
                                                ->where('is_line_article', 1);
            })
                                    ->where('status', 1)
                                    ->whereHas('article', function ($query) {
                                        $query->active();
                                    })
                                    ->whereHas('article.mainCategory', function ($query) use ($matchCategoriesLimitKey) {
                                            $query->filterAdvertorial()
                                                    ->whereNotIn('categories_id', $matchCategoriesLimitKey);
                                    })
                                    ->with('article.mainCategory')
                                    ->with('article.recommendations.mainCategory')
                                    ->whereNull('release_date')
                                    ->orderBy('created_at', 'ASC')
                                    ->get();

            $add_datas = collect();

            foreach ($searchDatas as $v) {
                if (array_key_exists($v->article->mainCategory->categories_id, $categoriesLimit)) {
                    if ($categoriesLimit[$v->article->mainCategory->categories_id] < self::LINE_ARTICLES_LIMIT) {
                        $add_datas = $add_datas->push($v);
                        $categoriesLimit[$v->article->mainCategory->categories_id] ++;
                    }
                } else {
                    $add_datas = $add_datas->push($v);
                    $categoriesLimit[$v->article->mainCategory->categories_id] = 1;
                }

                if ($add_datas->count() == $add_num) {
                    break;
                }
            }

            foreach ($add_datas as $v) {
                $row = LineArticlesModel::find($v->id);
                $row->update(['release_date' => Carbon::now()->format('Y-m-d')]);
                $datas->push($v);
            }
        }

        $UUID = 'tvbs_health_article' . Carbon::now()->format('YmdHis');
        $time = Carbon::now()->timestamp . '000';

        return response()->xml('third_party_feed.line_articles.api_template', [
            'UUID' => $UUID,
            'time' => $time,
            'articles' => LineArticlesCollection::collection($datas ?? [])->toArray(request()),
        ]);
    }

    /**
     * @return Response
     */
    public function mixerboxArticles(): Response
    {
        $date = now();

        $articles = $this->service->mixerboxRss($date, self::MIXERBOX_ARTICLES_TOTAL, self::MIXERBOX_ARTICLES_LIMIT);

        \URL::forceRootUrl(config('constants.frontend_url'));

        return response()->xml('third_party_feed.mixerbox_articles.api_template', [
            'title' => '健康2.0',
            'description' => '《健康2.0》是全台唯一網站與電視節目、AI科技融合的健康傳媒，也是最受醫界肯定、全民影音觀看流量最高的健康傳播品牌，能讓您輕鬆看、輕鬆學、輕鬆做，維持健康好活力！',
            'link' => route('api.mixerbox_articles'),
            'today' => now()->format('D, d M Y H:i:s O'),
            'language' => 'zh-TW',
            'articles' => MixerboxArticlesCollection::collection($articles)->toArray(request()),
        ]);
    }
}
