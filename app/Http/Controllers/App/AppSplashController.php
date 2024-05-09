<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\App\AppSplashModel;
use Session;

class AppSplashController extends Controller
{
    protected AppSplashModel $AppSplashModel;
    private $role_name = 'app_splash';
    private $site_name = 'app_splash';

    protected $validateRules = [
        'android_image' => 'required|regex:/jpeg$/',
        'iOS_image' => 'required|regex:/png$/',
        'start' => ['required', 'date', 'date_format:Y-m-d H:i:s'],
        'end' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after:start']
    ];
    protected $validateAttributes = [
        'android_image' => 'Android圖片',
        'iOS_image' => 'iOS圖片',
        'start' => '上架時間',
        'end' => '下架時間',
    ];
    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'date_format' => ':attribute 格式不符'
    ];
    public $editField = [
        [
            'title' => '狀態',
            'type' => 'select',
            'name' => 'status',
            'placeholder' => '請選擇狀態',
            'required' => TRUE,
            'id' => 'status',
            'option' => [
                0 => '下架',
                1 => '上架'
            ]
        ],
        [
            'title' => 'Android圖片',
            'type' => 'text',
            'name' => 'android_image',
            'placeholder' => '請輸入Android圖片位置',
            'required' => TRUE,
            'id' => 'android_image',
            'hint' => '請使用1080*1920 jpeg圖檔'
        ],
        [
            'title' => 'iOS圖片',
            'type' => 'text',
            'name' => 'iOS_image',
            'placeholder' => '請輸入iOS圖片位置',
            'required' => TRUE,
            'id' => 'iOS_image',
            'hint' => '請使用1080*1920 png圖檔'
        ],
        [
            'title' => '上架時間',
            'type' => 'custome-date-start',
            'name' => 'start',
            'placeholder' => '請輸入上架時間',
            'required' => TRUE,
            'id' => 'start',
            'class' => 'col-md-6'
        ],
        [
            'title' => '下架時間',
            'type' => 'custome-date-end',
            'name' => 'end',
            'placeholder' => '請輸入下架時間',
            'required' => TRUE,
            'id' => 'end',
            'class' => 'col-md-6'
        ],
    ];

    /**
     * WebMenuController constructor.
     * @param AdminRolesService $admin_roles
     * @param AppSplashModel $AppSplashModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        AppSplashModel $AppSplashModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->AppSplashModel = $AppSplashModel;
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
        $page_limit = 20;
        $role_name = $this->role_name;
        $site_name = $this->site_name;
        $field = array('狀態', 'Android圖片', 'iOS圖片', '開始時間', '結束時間');
        $datas = $this->AppSplashModel->latest()->paginate($page_limit);
        $editField = $this->editField;

        return view('app.splash.index', compact('role_name', 'site_name', 'field', 'datas', 'editField'));
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
        $request->merge(['start' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('start'))))]);
        $request->merge(['end' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('end'))))]);

        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);


        $request->request->add([
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->AppSplashModel->create($request->input());

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
        $result =  $this->AppSplashModel->find($id);
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
        $request->merge(['start' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('start'))))]);
        $request->merge(['end' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('end'))))]);

        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        $row = $this->AppSplashModel->find($id);

        $request->request->add([
            'updated_user' => auth()->user()->name
        ]);

        $row->update($request->input());

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
}
