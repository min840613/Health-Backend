<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\Categories\MainCategoriesModel;
use App\Models\Categories\MenuListModel;
use App\Repositories\ArticlesRepository;
use Session;

class MainCategoriesController extends Controller
{
    protected MainCategoriesModel $MainCategoriesModel;
    protected MenuListModel $MenuListModel;
    protected ArticlesRepository $ArticlesRepository;

    private $role_name = 'main_categories';
    private $site_name = 'main_categories';

    protected $validateRules = [
        'name' => 'required|max:6',
        'en_name' => 'required|regex:/^(?!.*[^a-zA-Z])/',
        'meta_title' => 'required|max:50',
        'description' => 'required|max:160',
        'categories_status' => 'required',
    ];

    protected $validateAttributes = [
        'name' => '分類名稱',
        'en_name' => '英文名稱',
        'meta_title' => 'Meta Title',
        'description' => 'Description',
        'categories_status' => '狀態',
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'regex' => ':attribute 請輸入英文',
        'max' => ':attribute 最多:max 個字',
    ];

    public $editField = [
        [
            'title' => '分類名稱',
            'type' => 'text',
            'name' => 'name',
            'placeholder' => '請輸入分類名稱',
            'required' => TRUE,
            'id' => 'name'
        ],
        [
            'title' => '英文名稱',
            'type' => 'text',
            'name' => 'en_name',
            'placeholder' => '請輸入英文名稱',
            'required' => TRUE,
            'id' => 'en_name',
            'hint' => '不得修改'
        ],
        [
            'title' => 'Meta Title',
            'type' => 'textarea',
            'row' => '2',
            'name' => 'meta_title',
            'placeholder' => '請輸入 Meta Title',
            'required' => TRUE,
            'id' => 'meta_title'
        ],
        [
            'title' => 'Meta Description',
            'type' => 'textarea',
            'row' => '3',
            'name' => 'description',
            'placeholder' => '請輸入 Meta Description',
            'required' => TRUE,
            'id' => 'description'
        ],
        [
            'title' => '狀態',
            'type' => 'select',
            'name' => 'categories_status',
            'placeholder' => '',
            'value' => 1,
            'required' => TRUE,
            'id' => 'categories_status',
            'option' => [
                1 => '上架',
                0 => '下架'
            ]
        ]
    ];

    /**
     * MainCategoriesController constructor.
     * @param AdminRolesService $admin_roles
     * @param MainCategoriesModel $MainCategoriesModel
     * @param MenuListModel $MenuListModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        MainCategoriesModel $MainCategoriesModel,
        MenuListModel $MenuListModel,
        ArticlesRepository $ArticlesRepository
    )
    {
        $this->admin_roles = $admin_roles;
        $this->MainCategoriesModel = $MainCategoriesModel;
        $this->MenuListModel = $MenuListModel;
        $this->ArticlesRepository = $ArticlesRepository;
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
        $page_limit = 20;
        $no_id = true;
        $search = true;
        $cond = array();
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if($keywords !== ''){
            $cond['keywords'] = $keywords;
        }
        $field = array('#', '主分類名', '英文名', '狀態', '最後修改時間', '子分類');

        $datas = $this->MainCategoriesModel::with('subCategories')
                    ->where(function($q) use($cond){
                        foreach ($cond as $key => $value) {
                            $q->where('name','like','%'.$value.'%');
                            $q->orwhere('en_name','like','%'.$value.'%');
                        }
                    })
                    ->where('categories_type', 1)
                    ->orderBy('sort_index', 'DESC')
                    ->paginate($page_limit)
                    ->appends($cond);

        $editField = $this->editField;

        return view('categories.main_categories.index', compact('role_name','site_name','search','keywords','field','no_id','datas','editField'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

        // 判斷英文名稱是否已存在
        if($this->MainCategoriesModel->where('en_name', $request->input('en_name'))->first()){
            return response()->json(['errMsg' => '英文名稱已重複'], 400);
        }

        $processedName = special_char_half_to_full($request->input('name'));

        $request->merge(['name' => $processedName]);

        $request->request->add([
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        \DB::transaction(function () use ($request) {
            $result = $this->MainCategoriesModel->create($request->input());

            //儲存WEB MENU
            $this->saveWebMenu($request, $result->categories_id, 'store');

            //儲存APP MENU
            $this->saveAppMenu($request, $result->categories_id, 'store');
        });

        return response()->json($request->input(), 200);
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
        $result =  $this->MainCategoriesModel->find($id);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        //當分類要下架要時，需判斷是否有所屬文章，沒有才允許下架
        if($request->input('categories_status') == '0'){

            $articles = $this->ArticlesRepository->additional(1, [], $id);

            if($articles->first()){
                return response()->json(['errMsg' => '此分類下有文章，無法下架'], 400);
            }
        }

        $row = $this->MainCategoriesModel->find($id);

        $en_name = $request->input('en_name');

        $request->request->remove('en_name');

        $processedName = special_char_half_to_full($request->input('name'));

        $request->merge(['name' => $processedName]);

        $request->request->add([
            'updated_user' => auth()->user()->name
        ]);

        \DB::transaction(function () use ($request, $row, $en_name, $id) {
            $row->update($request->input());

            $request->request->add([
                'en_name' => $en_name
            ]);

            //儲存WEB MENU
            $this->saveWebMenu($request, $id, 'update');

            //儲存APP MENU
            $this->saveAppMenu($request, $id, 'update');
        });

        return response()->json($request->input(), 200);
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

    public function saveWebMenu($request, $categories_id, $mode = 'store')
    {
        $is_app = 0;

        if($mode == 'store'){
            $latestSortOfMenu = $this->MenuListModel->where('is_app', $is_app)->orderBy('sort', 'desc')->first();
            $saveMenuArray = [
                'title' => $request->input('name'),
                'layout' => 2,
                'url' => $request->input('en_name'),
                'is_app' => $is_app,
                'sort' => isset($latestSortOfMenu->sort) ? $latestSortOfMenu->sort + 1 : 1,
                'categories_id' => $categories_id,
                'menu_list_status' => 0,
                'created_user' => auth()->user()->name,
                'updated_user' => $request->input('updated_user')
            ];
            $this->MenuListModel->create($saveMenuArray);
        }

        if($mode == 'update'){
            $saveMenuArray = [
                'title' => $request->input('name'),
                'layout' => 2,
                'is_app' => $is_app,
                'updated_user' => $request->input('updated_user')
            ];

            if((int)$request->input('categories_status') == 0){
                $saveMenuArray['menu_list_status'] = $request->input('categories_status');
            }

            $row = $this->MenuListModel->where('categories_id', $categories_id)
                                        ->where('url', $request->input('en_name'))
                                        ->where('is_app', $is_app)
                                        ->first();

            if($row){
                $row->update($saveMenuArray);
            }else{
                $this->saveWebMenu($request, $categories_id, 'store');
            }

        }
    }

    public function saveAppMenu($request, $categories_id, $mode = 'store')
    {
        $is_app = 1;

        if($mode == 'store'){
            $latestSortOfMenu = $this->MenuListModel->where('is_app', $is_app)->orderBy('sort', 'desc')->first();
            $saveMenuArray = [
                'title' => $request->input('name'),
                'layout' => 2,
                'url' => $request->input('en_name'),
                'is_app' => $is_app,
                'sort' => isset($latestSortOfMenu->sort) ? $latestSortOfMenu->sort + 1 : 1,
                'categories_id' => $categories_id,
                'menu_list_status' => 0,
                'created_user' => auth()->user()->name,
                'updated_user' => $request->input('updated_user')
            ];
            $this->MenuListModel->create($saveMenuArray);
        }
        if($mode == 'update'){

            $saveMenuArray = [
                'title' => $request->input('name'),
                'layout' => 2,
                'is_app' => $is_app,
                'updated_user' => $request->input('updated_user')
            ];

            if((int)$request->input('categories_status') == 0){
                $saveMenuArray['menu_list_status'] = $request->input('categories_status');
            }

            $row = $this->MenuListModel->where('categories_id', $categories_id)
                                        ->where('url', $request->input('en_name'))
                                        ->where('is_app', $is_app)
                                        ->first();

            if($row){
                $row->update($saveMenuArray);
            }else{
                $this->saveAppMenu($request, $categories_id, 'store');
            }
        }
    }
}
