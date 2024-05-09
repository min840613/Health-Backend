<?php

namespace App\Http\Controllers\HomeArea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\HomeArea\RightBarModel;
use App\Models\Categories\MainCategoriesModel;
use Session;

class RightBarController extends Controller
{
    protected RightBarModel $RightBarModel;
    protected MainCategoriesModel $MainCategoriesModel;

    private $role_name = 'right_bar';
    private $site_name = 'right_bar';

    protected $validateRules = [
        'name' => 'required|max:10',
        'main_category' => 'required|numeric|gt:0',
        'sub_category' => 'filled|numeric',
        'article_require_master' => 'filled|numeric',
        'status' => 'filled|numeric',
    ];

    protected $validateAttributes = [
        'name' => '版位名稱',
        'main_category' => '對應主分類',
        'sub_category' => '對應子分類',
        'article_require_master' => '文章需含區塊',
        'status' => '狀態',
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'filled' => ':attribute 為必填',
        'numeric' => ':attribute 必須為數字',
        'regex' => '請輸入正確:attribute',
        'max' => ':attribute 最多:max 個字',
        'gt' => '請選取 :attribute',
    ];

    /**
     * MainCategoriesController constructor.
     * @param AdminRolesService $admin_roles
     * @param RightBarModel $MainCategoriesRightBarModelModel
     * @param MainCategoriesModel $MainCategoriesModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        RightBarModel $RightBarModel,
        MainCategoriesModel $MainCategoriesModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->RightBarModel = $RightBarModel;
        $this->MainCategoriesModel = $MainCategoriesModel;
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
        $field = array('位置', '版位名稱', '對應分類', '狀態', '動作', '排序');

        $datas = $this->RightBarModel::with('mainCategory')
                                    ->with('subCategory')
                                    // ->orderBy('status', 'desc')
                                    ->orderBy('sort', 'asc')
                                    ->get();

        $editField = [];

        return view('homearea.right_bar.index', compact('role_name','site_name','search','keywords','field','no_id','datas','editField'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mainCategories = $this->MainCategoriesModel::where('categories_status', 1)
                                ->where(function ($query) {
                                    $query->where('categories_type', 1)
                                            ->orWhere('categories_type', 2);
                                })
                                ->groupBy('categories_id')
                                ->get();

        return view('homearea.right_bar.create', [
            'role_name' => $this->role_name,
            'site_name' => $this->site_name,
            'mainCategories' => $mainCategories,
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
        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        $request->request->add([
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->RightBarModel->create($request->input());

        return redirect()->route('right_bar.index')->with('success', '新增成功');
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
    public function edit($id)
    {
        $result = $this->RightBarModel::find($id);

        $categories = $this->MainCategoriesModel::with(['subCategories' => function($query) {
                                    $query->where('status', 1);
                                }])
                                ->where('categories_status', 1)
                                ->where(function ($query) {
                                    $query->where('categories_type', 1)
                                            ->orWhere('categories_type', 2);
                                })
                                ->groupBy('categories_id')
                                ->get()
                                ->keyBy('categories_id');

        return view('homearea.right_bar.edit', [
            'role_name' => $this->role_name,
            'site_name' => $this->site_name,
            'categories' => $categories,
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
        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        $request->request->add([
            'sub_category' => $request->input('sub_category', 0),
            'updated_user' => auth()->user()->name
        ]);

        $right_bar_entity = $this->RightBarModel->find($id);
        $right_bar_entity->update($request->input());

        return redirect()->route('right_bar.index')->with('success', '編輯成功');
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
        $rightBarRequest = $request->input('rightBar');
        $sortId = explode(',', $request->input('sortId'));

        foreach($sortId as $v){
            $rightBarId = $rightBarRequest[$v]['id'];
            $row = $this->RightBarModel->find($rightBarId);
            $rightBarUpdata = [
                'sort' => $position,
                'updated_user' => auth()->user()->name
            ];
            $row->update($rightBarUpdata);
            $position++;
        }
        return response()->json($request->input(), 200);
    }
}
