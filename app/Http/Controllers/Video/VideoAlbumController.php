<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\VideoAlbum;

class VideoAlbumController extends Controller
{
    protected VideoAlbum $VideoAlbumModel;

    private $role_name = 'video_album';
    private $site_name = 'video_album';

    protected $validateRules = [
        'title' => 'required|max:120',
    ];

    protected $validateAttributes = [
        'title' => '分類名稱',
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'max' => ':attribute 最多:max 個字'
    ];

    public $editField = [
        [
            'title' => '分類名稱',
            'type' => 'text',
            'name' => 'title',
            'placeholder' => '請輸入分類名稱',
            'required' => TRUE,
            'id' => 'title'
        ],
    ];

     /**
     * VideoAlbumController constructor.
     * @param AdminRolesService $admin_roles
     * @param VideoAlbum $VideoAlbumModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        VideoAlbum $VideoAlbumModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->VideoAlbumModel = $VideoAlbumModel;
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

        $field = array('分類名稱', '編輯', '刪除');

        $datas = $this->VideoAlbumModel->latest()->get();

        $editField = $this->editField;

        return view('video.index', compact('role_name','site_name','field','datas','editField'));
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

        $input['title'] =  $request->input('title');


        $this->VideoAlbumModel->create($input);

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
        $result =  $this->VideoAlbumModel->find($id);
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

        $album = $this->VideoAlbumModel->find($id);

        $input['title'] = $request->input('title');

        $album->update($input);

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
        $album = $this->VideoAlbumModel->find($id);
        return response()->json($album->delete(), 200);
    }
}
