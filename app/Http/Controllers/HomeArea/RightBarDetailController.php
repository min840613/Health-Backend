<?php

namespace App\Http\Controllers\HomeArea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\HomeArea\RightBarModel;
use App\Models\HomeArea\RightBarDetailModel;
use App\Models\Categories\MainCategoriesModel;
use App\Models\Articles\KeyvisualModel;
use App\Models\Articles\ArticleModel;
use App\Enums\MainCategoriesType;
use Session;

class RightBarDetailController extends Controller
{
    protected RightBarModel $RightBarModel;
    protected RightBarDetailModel $RightBarDetailModel;
    protected MainCategoriesModel $MainCategoriesModel;
    protected KeyvisualModel $KeyvisualModel;

    private $role_name = 'right_bar_detail';
    private $site_name = 'right_bar_detail';

    protected $validateRules = [
        'name' => 'required|max:26',
        'article_id' => 'required|numeric|gt:0',
        'published_at' => ['required', 'date_format:Y-m-d H:i', 'date'],
        'published_end' => ['required', 'date_format:Y-m-d H:i', 'date', 'after:published_at'],
        'status' => 'required|numeric',
    ];

    protected $validateAttributes = [
        'name' => '頭條標題',
        'article_id' => '文章ID',
        'published_at' => '發布時間',
        'published_end' => '下架時間',
        'status' => '狀態',
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'filled' => ':attribute 為必填',
        'numeric' => ':attribute 必須為數字',
        'regex' => '請輸入正確:attribute',
        'max' => ':attribute 最多:max 個字',
        'gt' => ':attribute 需大於 :gt',
        'date_format' => ':attribute 格式不符'
    ];

    /**
     * MainCategoriesController constructor.
     * @param AdminRolesService $admin_roles
     * @param RightBarModel $RightBarModel
     * @param RightBarDetailModel $RightBarDetailModel
     * @param MainCategoriesModel $MainCategoriesModel
     * @param KeyvisualModel $KeyvisualModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        RightBarModel $RightBarModel,
        RightBarDetailModel $RightBarDetailModel,
        MainCategoriesModel $MainCategoriesModel,
        KeyvisualModel $KeyvisualModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->RightBarModel = $RightBarModel;
        $this->RightBarDetailModel = $RightBarDetailModel;
        $this->MainCategoriesModel = $MainCategoriesModel;
        $this->KeyvisualModel = $KeyvisualModel;
        $this->middleware = $this->admin_roles->AdminRoles($this->role_name)->middleware;
        $this->function_role = $this->admin_roles->AdminRoles($this->role_name)->function_role;
        $this->menu_list = $this->admin_roles->AdminRoles($this->role_name)->menu_list;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($rightBarId)
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
        $field = array('位置', '版位名稱', '文章ID', '標題', '圖片', '上架時間', '下架時間', '狀態', '動作', '排序');

        $rightBarArray = $this->RightBarModel::orderBy('sort', 'asc')
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray();

        $rightBar[0] = '請選擇';
        $rightBar = $rightBar + $rightBarArray;

        if(empty($rightBarId)){
            $datas = [];
        }else{

            $datas = $this->RightBarModel::with(['detail' => function ($query){
                                $query->orderBy('sort', 'asc')
                                        ->with('article');
                            }])
                            ->with('mainCategory')
                            ->with('subCategory')
                            ->where('id', $rightBarId)
                            ->first();
        }
        $editField = [];

        return view('homearea.right_bar_detail.index', compact('role_name','site_name','search','keywords','field','no_id','rightBar','datas','editField'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($rightBarId)
    {
        if(empty($rightBarId)){
            return redirect()->route('detail.index', ['right_bar' => 0])->withErrors('請先選擇版位');
        }

        $rightBar = $this->RightBarModel::find($rightBarId)
                                    ->load('mainCategory')
                                    ->load('subCategory');

        $subCategoryName = '';

        if(!is_null($rightBar->subCategory)){
            $subCategoryName = $rightBar->subCategory->name;
        }

        return view('homearea.right_bar_detail.create', [
            'role_name' => $this->role_name,
            'site_name' => $this->site_name,
            'rightBar' => $rightBar,
            'subCategoryName' => $subCategoryName,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($rightBarId, Request $request)
    {
        // $request->merge(['published_at' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('published_at'))))]);
        // $request->merge(['published_end' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('published_end'))))]);

        $master_type = $request->input('master_type');
        $main_category = $request->input('main_category');
        $sub_category = $request->input('sub_category');

        $request->request->remove('master_type');
        $request->request->remove('main_category');
        $request->request->remove('sub_category');

        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        if($request->input('status') == '1'){
            $publish_at = $request->input('published_at');
            $publish_end = $request->input('publish_end');

            $keyvisual = $this->KeyvisualModel::where('source_id', $request->input('article_id'))
                                            ->where('status', 1)
                                            ->where(function ($query) use ($publish_at) {
                                                $query->where('start', '<=', date('Y-m-d H:i:s', strtotime($publish_at)))
                                                        ->where('end', '>=', date('Y-m-d H:i:s', strtotime($publish_at)));
                                            })
                                            ->orwhere(function ($query) use ($publish_end) {
                                                $query->where('start', '<=', date('Y-m-d H:i:s', strtotime($publish_end)))
                                                        ->where('end', '>=', date('Y-m-d H:i:s', strtotime($publish_end)));
                                            })
                                            ->get();

            if($keyvisual->first()){
                return back()->withErrors(['此文章ID在此發佈區間，於頭條管理有重複']);
            }

            $article = ArticleModel::when(!empty($master_type), function ($query) use ($master_type) {
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
                                    $query->where('categories_id', $main_category)
                                            ->whereNull('parent');
                                });
                                $query
                                // ->where('sort', 0)
                                ->whereNot('categories_type', MainCategoriesType::ADVERTORIAL);
                            })
                            ->where('articles_id', $request->input('article_id'))
                            ->active()
                            ->get();

            if(!$article->first()){
                return back()->withErrors(['此文章ID不存在或已下架或不屬於此版位設定']);
            }
        }

        $request->request->add([
            'right_bar_id' => $rightBarId,
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->RightBarDetailModel->create($request->input());

        return redirect()->route('detail.index', ['right_bar' => $rightBarId])->with('success', '新增成功');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($rightBarId, $id)
    {
        if(empty($id)){
            return redirect()->route('detail.index', ['right_bar' => $rightBarId])->withErrors('發生錯誤');
        }

        $rightBar = $this->RightBarModel::with(['detail' => function ($query) use($id) {
                                $query->where('id', $id)
                                        ->with('article')
                                        ->first();
                            }])
                            ->with('mainCategory')
                            ->with('subCategory')
                            ->where('id', $rightBarId)
                            ->first();

        $subCategoryName = '';

        if(!is_null($rightBar->subCategory)){
            $subCategoryName = $rightBar->subCategory->name;
        }

        return view('homearea.right_bar_detail.edit', [
            'role_name' => $this->role_name,
            'site_name' => $this->site_name,
            'rightBar' => $rightBar,
            'subCategoryName' => $subCategoryName,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $rightBarId, $id)
    {
        // $request->merge(['published_at' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('published_at'))))]);
        // $request->merge(['published_end' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('published_end'))))]);
        $master_type = $request->input('master_type');
        $main_category = $request->input('main_category');
        $sub_category = $request->input('sub_category');

        $request->request->remove('master_type');
        $request->request->remove('main_category');
        $request->request->remove('sub_category');

        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        if($request->input('status') == '1'){
            $publish_at = $request->input('published_at');
            $publish_end = $request->input('publish_end');

            $keyvisual = $this->KeyvisualModel::where('source_id', $request->input('article_id'))
                                            ->where('status', 1)
                                            ->where(function ($query) use ($publish_at) {
                                                $query->where('start', '<=', date('Y-m-d H:i:s', strtotime($publish_at)))
                                                        ->where('end', '>=', date('Y-m-d H:i:s', strtotime($publish_at)));
                                            })
                                            ->orwhere(function ($query) use ($publish_end) {
                                                $query->where('start', '<=', date('Y-m-d H:i:s', strtotime($publish_end)))
                                                        ->where('end', '>=', date('Y-m-d H:i:s', strtotime($publish_end)));
                                            })
                                            ->get();

            if($keyvisual->first()){
                return back()->withErrors(['此文章ID在此發佈區間，於頭條管理有重複']);
            }

            $article = ArticleModel::when(!empty($master_type), function ($query) use ($master_type) {
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
                                                $query->where('categories_id', $main_category)
                                                        ->whereNull('parent');
                                            });
                                            $query
                                            // ->where('sort', 0)
                                            ->whereNot('categories_type', MainCategoriesType::ADVERTORIAL);
                                        })
                                        ->where('articles_id', $request->input('article_id'))
                                        ->active()
                                        ->get();

            if(!$article->first()){
                return back()->withErrors(['此文章ID不存在或已下架或不屬於此版位設定']);
            }
        }

        $request->request->add([
            'updated_user' => auth()->user()->name
        ]);

        $right_bar_detail_entity = $this->RightBarDetailModel->find($id);
        $right_bar_detail_entity->update($request->input());

        return redirect()->route('detail.index', ['right_bar' => $rightBarId])->with('success', '編輯成功');
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
        $rightBarDetailRequest = $request->input('rightBarDetail');
        $sortId = explode(',', $request->input('sortId'));

        foreach($sortId as $v){
            $rightBaDetailrId = $rightBarDetailRequest[$v]['id'];
            $row = $this->RightBarDetailModel->find($rightBaDetailrId);
            $rightBarDetailUpdata = [
                'sort' => $position,
                'updated_user' => auth()->user()->name
            ];
            $row->update($rightBarDetailUpdata);
            $position++;
        }
        return response()->json($request->input(), 200);
    }
}
