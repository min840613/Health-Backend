<?php

namespace App\Http\Controllers\Authors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\Authors\AuthorsModel;
use Session;

class AuthorsController extends Controller
{
    protected AuthorsModel $AuthorsModel;

    private $role_name = 'authors';
    private $site_name = 'authors';

    protected $validateRules = [
        'name' => 'required|max:50',
        'status' => 'required',
    ];

    protected $validateAttributes = [
        'name' => '名稱',
        'status' => '狀態',
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'max' => ':attribute 最多:max 個字',
    ];

    public $editField = [
        [
            'title' => '名稱',
            'type' => 'text',
            'name' => 'name',
            'placeholder' => '請輸入名稱',
            'required' => TRUE,
            'id' => 'name',
            'class' => 'col-10'
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
        ]
    ];

    /**
     * AuthorsController constructor.
     * @param AdminRolesService $admin_roles
     * @param AuthorsModel $AuthorsModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        AuthorsModel $AuthorsModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->AuthorsModel = $AuthorsModel;
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
        $field = array('#','名稱','狀態');

        $datas = $this->AuthorsModel::where(function($q) use($cond){
                        foreach ($cond as $key => $value) {
                            $q->where('name','like','%'.$value.'%');
                        }
                    })
                    ->orderBy('id', 'ASC')
                    ->paginate($page_limit)
                    ->appends($cond);

        $editField = $this->editField;

        return view('authors.authors.index', compact('role_name','site_name','search','keywords','field','no_id','datas','editField'));
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

        $processedName = special_char_half_to_full($request->input('name'));

        $request->merge(['name' => $processedName]);

        $request->request->add([
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->AuthorsModel->create($request->input());

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
        $result =  $this->AuthorsModel->find($id);
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
        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        $row = $this->AuthorsModel->find($id);

        $processedName = special_char_half_to_full($request->input('name'));

        $request->merge(['name' => $processedName]);

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
