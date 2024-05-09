<?php

namespace App\Http\Controllers\Encyclopedia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\Encyclopedia\ArticlesCategoriesModel;

class ArticleTypeController extends Controller
{

    protected ArticlesCategoriesModel $ArticlesCategoriesModel;

    private $role_name = 'article_type';
    private $site_name = 'article_type';

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
            'title' => '狀態',
            'type' => 'select',
            'name' => 'status',
            'placeholder' => '',
            'value' => 1,
            'required' => TRUE,
            'id' => 'status',
            'option' => [
                1 => '上架',
                0 => '下架'
            ]
        ],
    ];

    protected $validateRules = [
        'name' => 'required|max:6',
        'status' => 'required|numeric'
    ];

    protected $validateAttributes = [
        'name' => '分類名稱',
        'status' => '狀態',
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'numeric' => ':attribute 必須為數字',
        'max' => ':attribute 最多:max 個字',
    ];

    /**
     * ArticleTypeController constructor.
     * @param AdminRolesService $admin_roles
     * @param ArticlesCategoriesModel $ArticlesCategoriesModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        ArticlesCategoriesModel $ArticlesCategoriesModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->ArticlesCategoriesModel = $ArticlesCategoriesModel;
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
        $search = false;
        $cond = array();
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if($keywords !== ''){
            $cond['keywords'] = $keywords;
        }

        $field = array('位置','文章類型','建立時間','更新時間','狀態','編輯','排序');
        $datas = $this->ArticlesCategoriesModel
                        ->orderBy('sort', 'ASC')
                        ->orderBy('id', 'DESC')
                        ->get();

        $editField = $this->editField;

        return view('encyclopedia.article_type.index', compact('role_name','site_name','search','keywords','field','no_id','datas','editField'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $processedName = special_char_half_to_full($request->input('name'));

        $request->merge(['name' => $processedName]);

        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        $request->request->add([
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->ArticlesCategoriesModel->create($request->input());

        return response()->json($request->input(), 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result =  $this->ArticlesCategoriesModel->find($id);
        return response()->json($result);
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
        $processedName = special_char_half_to_full($request->input('name'));
        $request->merge(['name' => $processedName]);

        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        $request->request->add([
            'updated_user' => auth()->user()->name
        ]);

        $articleType = $this->ArticlesCategoriesModel->find($id);

        if($articleType){
            $articleType->update($request->input());
            return response()->json($request->input(), 200);
        }else{
            return response()->json(['errMsg' => '此文章類型不存在'], 400);
        }
    }

    public function saveSort(Request $request)
    {
        $position = 1;
        $articleTypeRequest = $request->input('articleType');
        $sortId = explode(',', $request->input('sortId'));

        foreach($sortId as $v){
            $articleTypeId = $articleTypeRequest[$v]['id'];
            $row = $this->ArticlesCategoriesModel->find($articleTypeId);
            $articleTypeUpdata = [
                'sort' => $position,
                'updated_user' => auth()->user()->name
            ];
            $row->update($articleTypeUpdata);
            $position++;
        }
        return response()->json($request->input(), 200);
    }
}
