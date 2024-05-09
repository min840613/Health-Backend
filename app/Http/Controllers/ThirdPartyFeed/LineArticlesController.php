<?php

namespace App\Http\Controllers\ThirdPartyFeed;

use App\Http\Controllers\Controller;
use App\Models\Categories\MainCategoriesModel;
use App\Models\ThirdPartyFeed\LineArticleConditionModel;
use App\Models\ThirdPartyFeed\LineArticlesModel;
use App\Models\Articles\ArticleModel;
use Illuminate\Http\Request;

class LineArticlesController extends Controller
{
    protected MainCategoriesModel $MainCategoriesModel;
    protected LineArticleConditionModel $LineArticleConditionModel;
    protected LineArticlesModel $LineArticlesModel;
    protected ArticleModel $ArticleModel;

    /**
     * MainCategoriesController constructor.
     * @param MainCategoriesModel $MainCategoriesModel
     * @param LineArticleConditionModel $LineArticleConditionModel
     * @param LineArticlesModel $LineArticlesModel
     * @param ArticleModel $ArticleModel
     */
    public function __construct(
        MainCategoriesModel $MainCategoriesModel,
        LineArticleConditionModel $LineArticleConditionModel,
        LineArticlesModel $LineArticlesModel,
        ArticleModel $ArticleModel
    )
    {
        $this->MainCategoriesModel = $MainCategoriesModel;
        $this->LineArticleConditionModel = $LineArticleConditionModel;
        $this->LineArticlesModel = $LineArticlesModel;
        $this->ArticleModel = $ArticleModel;
    }

    public function conditionList()
    {
        $role_name = 'line_articles_condition';
        $site_name = 'line_articles_condition';

        $page_limit = 20;
        $no_id = true;
        $search = false;
        $cond = array();
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if($keywords !== ''){
            $cond['keywords'] = $keywords;
        }

        $field = array('分類ID', '分類英文名', '分類中文名', '更新人員');

        $categories = $this->MainCategoriesModel::filterAdvertorial()
                        ->active()
                        ->orderBy('sort_index', 'DESC')
                        ->get()
                        ->pluck('name', 'categories_id');

        $datas = $this->LineArticleConditionModel::paginate($page_limit)
                        ->appends($cond);

        return view('third_party_feed.line_articles.condition', compact('role_name','site_name','search','keywords','field','no_id','datas','categories'));
    }

    public function conditionSave(Request $request)
    {
        $categoriesArray = $request->input('categories');
        if(empty($categoriesArray)){
            return response()->json(['errMsg' => '請至少選擇一個分類'], 400);
        }
        $categories = $this->MainCategoriesModel::whereIn('categories_id', $categoriesArray)
                            ->get()
                            ->keyBy('categories_id')
                            ->toArray();
        $insertData = [];
        foreach($categoriesArray as $v){
            $insertData[] = [
                'category_id' => (int)$v,
                'category_en_name' => $categories[$v]['en_name'],
                'category_name' => $categories[$v]['name'],
                'created_user' => auth()->user()->name,
                'updated_user' => auth()->user()->name
            ];
        }

        \DB::transaction(function () use ($insertData) {
            $this->LineArticleConditionModel::query()->delete();
            $this->LineArticleConditionModel->insert($insertData);
        });

        return response()->json($insertData, 200);
    }

    public function articlesList(Request $request)
    {
        $role_name = 'line_articles';
        $site_name = 'line_articles';

        $page_limit = 20;
        $no_id = true;
        $search = false;
        $has_checkbox = true;
        $cond = array();
        $cond['search_category_id'] = $request->has('search_category_id') ? $request->input('search_category_id') : '0';
        $cond['search_release_date'] = $request->has('search_release_date') ? $request->input('search_release_date') : 'no';
        if($cond['search_release_date'] == 'yes'){
            $has_checkbox = false;
            $field = array('文章ID', '文章標題', '文章主分類', '供稿時間', '加入時間', '排程狀態');
        }else{
            $field = array('文章ID', '文章標題', '文章主分類', '文章發稿時間', '加入時間', '排程狀態');
        }
        $cond['search_order'] = $request->has('search_order') ? $request->input('search_order') : 'desc';

        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if($keywords !== ''){
            $cond['keywords'] = $keywords;
        }
        $has_act = false;

        $categories = $this->LineArticleConditionModel::get();

        $datas = $this->LineArticlesModel::when($cond['search_release_date'] == 'yes', function ($query) use ($request){
                            $query->whereNotNull('release_date')
                                    ->whereBetween('release_date', [$request->input('search_release_start'), $request->input('search_release_end')]);
                        })
                        ->when($cond['search_release_date'] == 'no', function ($query) {
                            $query->whereNull('release_date');
                        })
                        ->when($cond['search_category_id'] != '0', function ($query) use ($cond) {
                            $query->whereHas('article.mainCategory', function ($query) use ($cond) {
                                $query->where('categories_id', $cond['search_category_id'])
                                        ->filterAdvertorial();
                            });
                        })
                        ->with('article.mainCategory')
                        ->orderBy('created_at', $cond['search_order'])
                        ->paginate($page_limit)
                        ->appends($cond);

        return view('third_party_feed.line_articles.index', compact('role_name','site_name','search','has_checkbox','has_act','cond','field','no_id','datas','categories'));
    }

    public function changeStatus(Request $request)
    {
        $Id = explode(',', $request->input('Id'));

        foreach($Id as $v){
            $row = $this->LineArticlesModel->find($v);
            $Updata = [
                'status' => 1,
                'updated_user' => auth()->user()->name
            ];
            $row->update($Updata);
        }

        return response()->json($request->input('Id'), 200);
    }

    public function delete(Request $request)
    {
        $Id = explode(',', $request->input('Id'));
        $ArticlesId = explode(',', $request->input('ArticlesId'));

        \DB::transaction(function () use ($Id, $ArticlesId){
            foreach($Id as $k => $v){
                $article = $this->ArticleModel->find($ArticlesId[$k]);
                $article->update(['is_line_article' => 0]);
                $this->LineArticlesModel::where('id',$v)->delete();
            }
        });

        return response()->json($request->input('Id'), 200);
    }
}
