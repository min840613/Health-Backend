<?php

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\Articles\EndTextModel;
use Session;

class EndTextController extends Controller
{
    protected EndTextModel $EndTextModel;

    private $role_name = 'end_text';
    private $site_name = 'end_text';

    protected $validateRules = [
        'short_title' => 'required|max:120',
        // 'text_type' => 'required',
        'content' => 'required',
        'status' => 'required',
        'url' => ['required','regex:/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i'],
        'published_at' => ['required', 'date_format:Y-m-d H:i', 'date'],
        'published_end' => ['required', 'date_format:Y-m-d H:i', 'date', 'after:published_at']
    ];

    protected $validateAttributes = [
        'short_title' => '標題',
        'text_type' => '類型',
        'content' => '內容',
        'status' => '狀態',
        'url' => '網址',
        'published_at' => '上架時間',
        'published_end' => '下架時間',
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'regex' => '請輸入正確:attribute',
        'max' => ':attribute 最多:max 個字',
        'date_format' => ':attribute 格式不符'
    ];

    public $select_type = [
        'title' => '搜尋類型',
        'type' => 'select',
        'name' => 'select_type',
        'placeholder' => '',
        'value' => 0,
        'required' => TRUE,
        'id' => 'select_type',
        'option' => [
            0 => '全部',
            1 => '警語',
            2 => '廣宣'
        ]
    ];
    public $editField = [
        [
            'title' => '標題',
            'type' => 'text',
            'name' => 'short_title',
            'placeholder' => '請輸入標題',
            'required' => TRUE,
            'id' => 'short_title'
        ],
        [
            'title' => '類型',
            'type' => 'select',
            'name' => 'text_type',
            'placeholder' => '',
            'value' => 1,
            'required' => TRUE,
            'id' => 'text_type',
            'option' => [
                1 => '警語',
                2 => '廣宣'
            ]
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
            'title' => '內容',
            'type' => 'textarea',
            'name' => 'content',
            'placeholder' => '請輸入內容',
            'required' => TRUE,
            'id' => 'content'
        ],
        [
            'title' => '上架時間',
            'type' => 'custome-date-start-common',
            'name' => 'published_at',
            'placeholder' => '請輸入上架時間',
            'required' => TRUE,
            'id' => 'published_at',
            'class' => 'col-md-6'
        ],
        [
            'title' => '下架時間',
            'type' => 'custome-date-end-common',
            'name' => 'published_end',
            'placeholder' => '請輸入下架時間',
            'required' => TRUE,
            'id' => 'published_end',
            'class' => 'col-md-6'
        ],
        [
            'title' => '狀態',
            'type' => 'select',
            'name' => 'status',
            'placeholder' => '',
            'value' => 0,
            'required' => TRUE,
            'id' => 'status',
            'option' => [
                0 => '下架',
                1 => '上架'
            ]
        ]
    ];

     /**
     * MainCategoriesController constructor.
     * @param AdminRolesService $admin_roles
     * @param EndTextModel $EndTextModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        EndTextModel $EndTextModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->EndTextModel = $EndTextModel;
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
        $no_id = false;
        $search = false;
        $cond = array();
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if($keywords !== ''){
            $cond['keywords'] = $keywords;
        }

        $select_type = isset($_GET['select_type']) ? $_GET['select_type'] : 0;
        if($select_type !== 0){
            $cond['select_type'] = $select_type;
        }

        $field = array('標題', '類型', '建立者', '上下架時間', '狀態', '編輯', '刪除', '排序');

        $datas = $this->EndTextModel
                    ->where(function($q) use($select_type){
                        if(!empty($select_type)){
                            $q->where('text_type', $select_type);
                        }
                    })
                    ->orderBy('order_num', 'ASC')
                    ->orderBy('updated_at', 'DESC')
                    ->get();

        $select_type = $this->select_type;
        $editField = $this->editField;

        return view('articles.end_text.index', compact('role_name','site_name','search','keywords','field','no_id','datas','select_type','editField'));
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

        // $request->merge(['published_at' => str_replace("T", " ", $request->input('published_at'))]);
        // $request->merge(['published_end' => str_replace("T", " ", $request->input('published_end'))]);

        if($request->input('text_type') == '1'){
            $request->request->remove('url');
            $request->request->remove('published_at');
            $request->request->remove('published_end');
            unset($this->validateRules['url'], $this->validateRules['published_at'], $this->validateRules['published_end']);
        }
        if($request->input('text_type') == '2'){
            $request->request->remove('content');
            unset($this->validateRules['content']);
        }

        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        $processedShortTitle = special_char_half_to_full($request->input('short_title'));
        $processedContent = trim($request->input('content'));
        // $request->merge(['content' => trim($request->input('content'))]);
        // filter_content($U_content,$this->config->item('s3_pic_trans'));

        $request->merge(['short_title' => $processedShortTitle]);
        $request->merge(['content' => $processedContent]);

        $request->request->add([
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->EndTextModel->create($request->input());

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
        $result =  $this->EndTextModel->find($id);
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
        if($id == 'save_sort'){
            $position = 1;
            $menuRequest = $request->input('end_text');
            $sortId = explode(',', $request->input('sortId'));

            foreach($sortId as $v){
                $endTextId = $menuRequest[$v]['text_id'];
                $row = $this->EndTextModel->find($endTextId);
                $menuUpdata = [
                    'order_num' => $position,
                    'updated_user' => auth()->user()->name
                ];
                $row->update($menuUpdata);
                $position++;
            }
            return response()->json($request->input(), 200);
        }

        $request->merge(['published_at' => str_replace("T", " ", $request->input('published_at'))]);
        $request->merge(['published_end' => str_replace("T", " ", $request->input('published_end'))]);

        if($request->input('text_type') == '1'){
            $request->request->remove('url');
            $request->request->remove('published_at');
            $request->request->remove('published_end');
            unset($this->validateRules['url'], $this->validateRules['published_at'], $this->validateRules['published_end']);
        }
        if($request->input('text_type') == '2'){
            $request->request->remove('content');
            unset($this->validateRules['content']);
        }

        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        $row = $this->EndTextModel->find($id);

        $processedShortTitle = special_char_half_to_full($request->input('short_title'));
        $processedContent = trim($request->input('content'));

        $request->merge(['short_title' => $processedShortTitle]);
        $request->merge(['content' => $processedContent]);
        // $request->merge(['content' => trim($request->input('content'))]);
        // filter_content($U_content,$this->config->item('s3_pic_trans'));

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
        $endText = $this->EndTextModel->find($id);
        return response()->json($endText->delete(), 200);
    }
}
