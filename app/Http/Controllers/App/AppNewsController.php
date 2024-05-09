<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\App\AppNewsModel;
use Session;

class AppNewsController extends Controller
{
    protected AppNewsModel $AppNewsModel;
    private $role_name = 'app_news';
    private $site_name = 'app_news';

    protected $validateRules = [
        'message' => 'required|max:120',
        'start' => ['required', 'date', 'date_format:Y-m-d H:i:s'],
        'end' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after:start']
    ];
    protected $validateAttributes = [
        'message' => '訊息',
        'start' => '上架時間',
        'end' => '下架時間',
    ];
    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'max' => ':attribute 最多:max 個字',
        'date_format' => ':attribute 格式不符'
    ];
    public $editField = [
        [
            'title' => '訊息',
            'type' => 'textarea',
            'name' => 'message',
            'placeholder' => '請輸入訊息',
            'required' => TRUE,
            'id' => 'message'
        ],
        [
            'title' => '上架時間',
            'type' => 'custome-date-start-common',
            'name' => 'start',
            'placeholder' => '請輸入上架時間',
            'required' => TRUE,
            'id' => 'start',
            'class' => 'col-md-6'
        ],
        [
            'title' => '下架時間',
            'type' => 'custome-date-end-common',
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
     * @param AppNewsModel $AppNewsModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        AppNewsModel $AppNewsModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->AppNewsModel = $AppNewsModel;
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
        $field = array('訊息', '開始時間', '結束時間', '動作');
        $datas = $this->AppNewsModel::find(1);
        $editField = $this->editField;

        return view('app.news.index', compact('role_name', 'site_name', 'field', 'datas', 'editField'));
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


        $request->request->add([
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->AppNewsModel->create($request->input());

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
        $result =  $this->AppNewsModel->find($id);
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

        $row = $this->AppNewsModel->find($id);

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
