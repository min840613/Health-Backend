<?php

namespace App\Http\Controllers\ThirdPartyFeed;

use App\Http\Controllers\Controller;
use App\Models\ThirdPartyFeed\YahooRssModel;
use App\Models\Articles\ArticleModel;
use App\Models\Articles\ArticlesFurtherReadingModel;
use Illuminate\Http\Request;

class YahooArticlesController extends Controller
{
    protected YahooRssModel $YahooRssModel;
    protected ArticleModel $ArticleModel;
    protected ArticlesFurtherReadingModel $ArticlesFurtherReadingModel;

     /**
     * YahooArticlesController constructor.
     * @param YahooRssModel $YahooRssModel
     * @param ArticleModel $ArticleModel
     * @param ArticlesFurtherReadingModel $ArticlesFurtherReadingModel
     */
    public function __construct(
        YahooRssModel $YahooRssModel,
        ArticleModel $ArticleModel,
        ArticlesFurtherReadingModel $ArticlesFurtherReadingModel
    )
    {
        $this->YahooRssModel = $YahooRssModel;
        $this->ArticleModel = $ArticleModel;
        $this->ArticlesFurtherReadingModel = $ArticlesFurtherReadingModel;
    }

    public function articlesList(Request $request)
    {
        $role_name = 'yahoo_articles';
        $site_name = 'yahoo_articles';

        $page_limit = 20;
        $no_id = true;
        $search = false;
        $has_checkbox = true;
        $cond = array();
        $cond['search_release_date'] = $request->has('search_release_date') ? $request->input('search_release_date') : 'no';
        if($cond['search_release_date'] == 'yes'){
            $has_checkbox = false;
            $field = array('文章ID', '文章標題', '供稿時間', '加入時間', '排程狀態');
        }else{
            $field = array('文章ID', '文章標題', '文章發稿時間', '加入時間', '排程狀態');
        }
        $cond['search_order'] = $request->has('search_order') ? $request->input('search_order') : 'desc';

        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if($keywords !== ''){
            $cond['keywords'] = $keywords;
        }
        $has_act = false;

        $datas = $this->YahooRssModel::when($cond['search_release_date'] == 'yes', function ($query) use ($request){
                            $query->whereNotNull('rss_release_date')
                                    ->whereBetween('rss_release_date', [$request->input('search_release_start'), $request->input('search_release_end')]);
                        })
                        ->when($cond['search_release_date'] == 'no', function ($query) {
                            $query->whereNull('rss_release_date');
                        })
                        ->with('article')
                        ->orderBy('created_at', $cond['search_order'])
                        ->paginate($page_limit)
                        ->appends($cond);

        return view('third_party_feed.yahoo.index', compact('role_name','site_name','search','has_checkbox','has_act','cond','field','no_id','datas'));
    }

    public function delete(Request $request)
    {
        $Id = explode(',', $request->input('Id'));
        $ArticlesId = explode(',', $request->input('ArticlesId'));

        \DB::transaction(function () use ($Id, $ArticlesId){
            foreach($Id as $k => $v){
                $article = $this->ArticleModel->find($ArticlesId[$k]);
                $article->update(['is_yahoo_rss' => 0]);
                $this->YahooRssModel::where('id',$v)->delete();
                $this->ArticlesFurtherReadingModel::where('article_id',$ArticlesId[$k])->where('type','yahoo')->delete();
            }
        });

        return response()->json($request->input('Id'), 200);
    }
}
