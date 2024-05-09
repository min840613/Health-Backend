<?php

namespace App\Http\Controllers\Galleries;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\ImageFavorite;

class ImageFavoriteController extends Controller
{
    protected ImageFavorite $ImageFavoriteModel;

    private $role_name = 'image_favorite';
    private $site_name = 'image_favorite';

    protected $validateRules = [
        'title' => 'required|max:120',
    ];

    protected $validateAttributes = [
        'title' => '圖片名稱',
        'url' => '圖片位置',
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'max' => ':attribute 最多:max 個字'
    ];

    public $editField = [
        [
            'title' => '圖片名稱',
            'type' => 'text',
            'name' => 'title',
            'placeholder' => '請輸入圖片名稱',
            'required' => TRUE,
            'id' => 'title'
        ],
        [
            'title' => '圖片位置',
            'type' => 'text',
            'name' => 'url',
            'placeholder' => '請輸入網址',
            'required' => TRUE,
            'id' => 'url'
        ],
    ];

     /**
     * ImageAlbumController constructor.
     * @param AdminRolesService $admin_roles
     * @param ImageFavorite $ImageFavoriteModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        ImageFavorite $ImageFavoriteModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->ImageFavoriteModel = $ImageFavoriteModel;
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

        $field = array('圖片名稱', '圖片', '編輯', '刪除');

        $datas = $this->ImageFavoriteModel->latest()->paginate($page_limit);

        $editField = $this->editField;

        return view('galleries.image_favorite.index', compact('role_name','site_name','field','datas','editField'));
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
        $input['url'] =  $request->input('url');


        $this->ImageFavoriteModel->create($input);

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
        $result =  $this->ImageFavoriteModel->find($id);
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

        $favorite = $this->ImageFavoriteModel->find($id);

        $input['title'] = $request->input('title');
        $input['url'] = $request->input('url');

        $favorite->update($input);

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
        $favorite = $this->ImageFavoriteModel->find($id);
        return response()->json($favorite->delete(), 200);
    }
}
