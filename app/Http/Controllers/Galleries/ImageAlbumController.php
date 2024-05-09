<?php

namespace App\Http\Controllers\Galleries;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\ImageAlbum;

class ImageAlbumController extends Controller
{
    protected ImageAlbum $ImageAlbumModel;

    private $role_name = 'image_album';
    private $site_name = 'image_album';

    protected $validateRules = [
        'title' => 'required|max:120',
    ];

    protected $validateAttributes = [
        'title' => '相簿名稱',
        'cover_image' => '封面',
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'max' => ':attribute 最多:max 個字'
    ];

    public $editField = [
        [
            'title' => '相簿名稱',
            'type' => 'text',
            'name' => 'title',
            'placeholder' => '請輸入相簿名稱',
            'required' => TRUE,
            'id' => 'title'
        ],
        [
            'title' => '相簿封面',
            'type' => 'text',
            'name' => 'cover_image',
            'placeholder' => '請輸入封面網址',
            'required' => FALSE,
            'id' => 'cover_image'
        ],
    ];

     /**
     * ImageAlbumController constructor.
     * @param AdminRolesService $admin_roles
     * @param ImageAlbum $ImageAlbumModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        ImageAlbum $ImageAlbumModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->ImageAlbumModel = $ImageAlbumModel;
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

        $field = array('相簿名稱', '編輯', '刪除');

        $datas = $this->ImageAlbumModel->latest()->get();

        $editField = $this->editField;

        return view('galleries.image_album.index', compact('role_name','site_name','field','datas','editField'));
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
        $input['cover_image'] =  $request->input('cover_image');


        $this->ImageAlbumModel->create($input);

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
        $result =  $this->ImageAlbumModel->find($id);
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

        $album = $this->ImageAlbumModel->find($id);

        $input['title'] = $request->input('title');
        $input['cover_image'] = $request->input('cover_image');

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
        $album = $this->ImageAlbumModel->find($id);
        return response()->json($album->delete(), 200);
    }
}
