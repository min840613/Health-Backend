<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\Categories\MenuListModel;
use Session;

class WebMenuController extends Controller
{
    protected MenuListModel $MenuListModel;

    private $role_name = 'web_menu';
    private $site_name = 'web_menu';

    protected $validateRules = [
        'title' => 'required|max:10',
        'url' => ['required','regex:/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i'],
        'blank' => 'required',
        'menu_list_status' => 'required'
    ];

    protected $validateAttributes = [
        'title' => '單元名稱',
        'url' => '網址',
        'blank' => '開啟方式',
        'menu_list_status' => '顯示狀態'
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'regex' => '請輸入正確 :attribute',
        'max' => ':attribute 最多:max 個字',
    ];

    public $editField = [
        [
            'title' => '單元名稱',
            'type' => 'text',
            'name' => 'title',
            'placeholder' => '請輸入單元名稱',
            'required' => TRUE,
            'id' => 'title'
        ],
        [
            'title' => '網址',
            'type' => 'text',
            'name' => 'url',
            'placeholder' => '請輸入網址',
            'required' => TRUE,
            'id' => 'url'
        ],
        [
            'title' => '開啟方式',
            'type' => 'select',
            'name' => 'blank',
            'placeholder' => '',
            'value' => 0,
            'required' => TRUE,
            'id' => 'blank',
            'option' => [
                0 => '同頁開啟',
                1 => '另開新頁'
            ]
        ],
        [
            'title' => '顯示狀態',
            'type' => 'select',
            'name' => 'menu_list_status',
            'placeholder' => '',
            'value' => 1,
            'required' => TRUE,
            'id' => 'menu_list_status',
            'option' => [
                1 => '顯示',
                0 => '不顯示'
            ]
        ]
    ];

    /**
     * WebMenuController constructor.
     * @param AdminRolesService $admin_roles
     * @param MenuListModel $MenuListModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        MenuListModel $MenuListModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->MenuListModel = $MenuListModel;
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
        $field = array('位置', '單元名稱', '網址', '開啟方式', '顯示狀態', '排序');

        $datas = $this->MenuListModel::where('is_app', 0)
                                    ->orderBy('sort', 'asc')
                                    ->get();

        $editField = $this->editField;
        return view('categories.menu.web_menu_index', compact('role_name','site_name','search','keywords','field','no_id','datas','editField'));
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

        $processedTitle = special_char_half_to_full($request->input('title'));

        $request->merge(['title' => $processedTitle]);

        $is_app = 0;

        $latestSortOfMenu = $this->MenuListModel->where('is_app', $is_app)->orderBy('sort', 'desc')->first();

        $request->request->add([
            'is_app' => $is_app,
            'sort' => isset($latestSortOfMenu->sort) ? $latestSortOfMenu->sort + 1 : 1,
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->MenuListModel->create($request->input());

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
        //
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
        $errMsg = '';

        foreach($request->input('menu') as $key => $val){
            foreach($val as $k => $v){
                if($k == 'title' || $k == 'url'){
                    if(empty($v)){
                        $errMsg = '位置第'.$key.'：'.$this->validateAttributes[$k].'為必填';
                        return response()->json(['errMsg' => $errMsg], 400);
                    }else{
                        if($k == 'title' && mb_strlen($v, "utf-8") > 10){
                            $errMsg = '位置第'.$key.'：'.$this->validateAttributes[$k].' 最多10個字';
                            return response()->json(['errMsg' => $errMsg], 400);
                        }

                        if($k == 'url' && !$val['categories_id']
                            && !preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i', $v))
                        {
                            $errMsg = '位置第'.$key.'：'.'請輸入正確 '.$this->validateAttributes[$k];
                            return response()->json(['errMsg' => $errMsg], 400);
                        }
                    }
                }
            }
        }

        $position = 1;
        $menuRequest = $request->input('menu');
        $sortId = explode(',', $request->input('sortId'));

        foreach($sortId as $v){
            $menuListId = $menuRequest[$v]['menu_list_id'];
            $row = $this->MenuListModel->find($menuListId);
            $menuUpdata = [
                'title' => special_char_half_to_full($menuRequest[$v]['title']),
                'url' => $menuRequest[$v]['url'],
                'blank' => $menuRequest[$v]['blank'],
                'menu_list_status' => $menuRequest[$v]['menu_list_status'],
                'sort' => $position,
                'is_app' => 0,
                'updated_user' => auth()->user()->name
            ];
            $row->update($menuUpdata);
            $position++;
        }

        return response()->json($request->input('menu'), 200);
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
}
