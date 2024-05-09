<?php

namespace App\Http\Controllers\HomeArea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\HomeArea\HomeTaxonModel;
use App\Models\Categories\MainCategoriesModel;
use App\Models\Articles\ArticleModel;
use Session;

class HomeTaxonController extends Controller
{
    protected HomeTaxonModel $HomeTaxonModel;
    protected MainCategoriesModel $MainCategoriesModel;
    protected ArticleModel $ArticleModel;

    private $role_name = 'home_taxon';
    private $site_name = 'home_taxon';

    protected $validateRules = [
        'name' => 'filled|max:10',
        'categories_id' => 'filled|numeric|gt:0',
        'article_id' => 'filled|numeric',
        'status' => 'filled',
        'published_at' => ['filled', 'date_format:Y-m-d H:i', 'date'],
        'published_end' => ['filled', 'date_format:Y-m-d H:i', 'date', 'after:published_at']
    ];

    protected $validateAttributes = [
        'name' => '名稱',
        'categories_id' => '分類',
        'article_id' => '置頂文章ID',
        'status' => '狀態',
        'published_at' => '置頂文章上架時間',
        'published_end' => '置頂文章下架時間',
    ];

    protected $validateMessages = [
        'filled' => ':attribute 為必填',
        'numeric' => ':attribute 必須為數字',
        'regex' => '請輸入正確:attribute',
        'max' => ':attribute 最多:max 個字',
        'gt' => '請選取 :attribute',
    ];

    /**
     * MainCategoriesController constructor.
     * @param AdminRolesService $admin_roles
     * @param MainCategoriesModel $MainCategoriesModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        HomeTaxonModel $HomeTaxonModel,
        MainCategoriesModel $MainCategoriesModel,
        ArticleModel $ArticleModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->HomeTaxonModel = $HomeTaxonModel;
        $this->MainCategoriesModel = $MainCategoriesModel;
        $this->ArticleModel = $ArticleModel;
        $this->middleware = $this->admin_roles->AdminRoles($this->role_name)->middleware;
        $this->function_role = $this->admin_roles->AdminRoles($this->role_name)->function_role;
        $this->menu_list = $this->admin_roles->AdminRoles($this->role_name)->menu_list;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role_name = $this->role_name;
        $site_name = $this->site_name;
        // $page_limit = 20;
        $no_id = true;
        $search = false;
        $cond = array();
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if($keywords !== ''){
            $cond['keywords'] = $keywords;
        }
        $field = array('位置', '單元名稱', '分類', '置頂文章ID', '文章主圖', '置頂時間', '狀態', '動作', '排序');

        $datas = $this->HomeTaxonModel::with('article')->orderBy('sort', 'asc')->get();
        // dd($datas);
        $mainCategories = $this->getMainCategories()['proccess_data'];
        $editField = [];
        return view('homearea.taxon.index', compact('role_name','site_name','search','keywords','field','no_id','datas','mainCategories','editField'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mainCategories = $mainCategories = $this->getMainCategories()['model_data'];

        return view('homearea.taxon.create', [
            'role_name' => $this->role_name,
            'site_name' => $this->site_name,
            'main_categories' => $mainCategories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->input('article_id')){
            unset($this->validateRules['article_id']);
            unset($this->validateRules['published_at']);
            unset($this->validateRules['published_end']);
            $request->merge(['published_at' => null]);
            $request->merge(['published_end' => null]);

            $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);
        }else{
            // $request->merge(['published_at' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('published_at'))))]);
            // $request->merge(['published_end' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('published_end'))))]);
            $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

            $article = $this->ArticleModel::with('mainCategories')
                            ->where('articles_id', $request->input('article_id'))
                            ->where('articles_status', 1)
                            ->first();
            if(!$article){
                return back()->withErrors(['文章ID：'.$request->input("article_id").'，不存在或已下架！']);
            }else{
                if(!in_array($request->input("categories_id"), $article->mainCategories->pluck('categories_id')->toArray())){
                    return back()->withErrors([$this->getMainCategories()['proccess_data'][$request->input("categories_id")].'，此分類不存在於此文章']);
                }
            }
        }

        $row = $this->HomeTaxonModel::where('categories_id', $request->input('categories_id'))
                    ->first();
        if($row){
            return back()->withErrors([$this->getMainCategories()['proccess_data'][$request->input("categories_id")].'，此分類已存在']);
        }

        if($request->input('status') == 1){
            $overThreeOn = $this->HomeTaxonModel::where('status', 1)
                                ->get();
            if($overThreeOn->count() >= 3){
                return back()->withErrors(['已有三個單元目前狀態為上架！']);
            }
        }

        $request->request->add([
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->HomeTaxonModel->create($request->input());

        return redirect()->route('home_taxon.index')->with('success', '新增成功');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mainCategories = $mainCategories = $this->getMainCategories()['model_data'];
        $result = $this->HomeTaxonModel::find($id);
        return view('homearea.taxon.edit', [
            'role_name' => $this->role_name,
            'site_name' => $this->site_name,
            'main_categories' => $mainCategories,
            'result' => $result
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if(!$request->input('article_id')){
            unset($this->validateRules['article_id']);
            unset($this->validateRules['published_at']);
            unset($this->validateRules['published_end']);
            $request->merge(['published_at' => null]);
            $request->merge(['published_end' => null]);
            $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);
        }else{
            // $request->merge(['published_at' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('published_at'))))]);
            // $request->merge(['published_end' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('published_end'))))]);
            $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

            $article = $this->ArticleModel::with('mainCategories')
                            ->where('articles_id', $request->input('article_id'))
                            ->where('articles_status', 1)
                            ->first();

            if(!$article){
                return back()->withErrors(['文章ID：'.$request->input("article_id").'，不存在或已下架！']);
            }else{
                if(!in_array($request->input("categories_id"), $article->mainCategories->pluck('categories_id')->toArray())){
                    return back()->withErrors([$this->getMainCategories()['proccess_data'][$request->input("categories_id")].'，此分類不存在於此文章']);
                }
            }
        }

        $row = $this->HomeTaxonModel::where('categories_id', $request->input('categories_id'))
                    ->whereNotIn('id', [$id])
                    ->first();
        if($row){
            return back()->withErrors([$this->getMainCategories()['proccess_data'][$request->input("categories_id")].'，此分類已存在']);
        }

        if($request->input('status') == 1){
            $overThreeOn = $this->HomeTaxonModel::where('status', 1)
                                ->whereNotIn('id', [$id])
                                ->get();
            if($overThreeOn->count() >= 3){
                return back()->withErrors(['已有三個單元目前狀態為上架！']);
            }
        }

        $home_taxon_entity = $this->HomeTaxonModel->find($id);
        $home_taxon_entity->update($request->input());

        return redirect()->route('home_taxon.index')->with('success', '編輯成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function saveSort(Request $request)
    {
        $position = 1;
        $homeTaxonRequest = $request->input('homeTaxon');
        $sortId = explode(',', $request->input('sortId'));

        foreach($sortId as $v){
            $homeTaxonId = $homeTaxonRequest[$v]['id'];
            $row = $this->HomeTaxonModel->find($homeTaxonId);
            $homeTaxonUpdata = [
                'sort' => $position,
                'updated_user' => auth()->user()->name
            ];
            $row->update($homeTaxonUpdata);
            $position++;
        }
        return response()->json($request->input(), 200);
    }

    public function getMainCategories()
    {
        $mainCategories = $this->MainCategoriesModel::where('categories_status', 1)
                                ->where(function ($query) {
                                    $query->where('categories_type', 1)
                                            ->orWhere('categories_type', 2);
                                })
                                ->groupBy('categories_id')
                                ->get();
        $mainCategoriesArray = [];
        foreach($mainCategories as $v){
            $mainCategoriesArray[$v->categories_id] = $v->name;
        }
        return ['model_data' => $mainCategories, 'proccess_data' => $mainCategoriesArray ];
    }
}
