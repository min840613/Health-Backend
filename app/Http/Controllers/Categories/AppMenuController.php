<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\Categories\MenuListModel;
use Session;

class AppMenuController extends Controller
{
    protected MenuListModel $MenuListModel;

    private $role_name = 'app_menu';
    private $site_name = 'app_menu';

    protected $validateAttributes = [
        'title' => '單元名稱',
        'url' => '網址',
        'blank' => '開啟方式',
        'menu_list_status' => '顯示狀態'
    ];

    public $layoutOption = [
        2 => '分類頁',
        5 => '專題頁',
        6 => '醫學百科'
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
        $field = array('位置', '英文名', '單元名稱', '版型選擇', '顯示狀態', '排序');

        $datas = $this->MenuListModel::where('is_app', 1)
                                    ->orderBy('sort', 'asc')
                                    ->get();

        $editField = [];
        $layoutOption = $this->layoutOption;
        return view('categories.menu.app_menu_index', compact('role_name','site_name','search','keywords','field','no_id','datas','editField','layoutOption'));
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
        //
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
                if($k == 'title'){
                    if(empty($v)){
                        $errMsg = '位置第'.$key.'：'.$this->validateAttributes[$k].'為必填';
                        return response()->json(['errMsg' => $errMsg], 400);
                    }else{
                        if($k == 'title' && mb_strlen($v, "utf-8") > 10){
                            $errMsg = '位置第'.$key.'：'.$this->validateAttributes[$k].' 最多10個字';
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
                'layout' => $menuRequest[$v]['layout'],
                'menu_list_status' => $menuRequest[$v]['menu_list_status'],
                'sort' => $position,
                'is_app' => 1,
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
