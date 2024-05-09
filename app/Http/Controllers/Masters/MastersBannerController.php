<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\Masters\MastersBannerModel;
use App\Models\Masters\DivisionsModel;
use App\Models\Masters\InstitutionsModel;
use App\Models\Masters\MastersModel;

class MastersBannerController extends Controller
{
    protected MastersBannerModel $MastersBannerModel;

    protected DivisionsModel $DivisionsModel;

    protected InstitutionsModel $InstitutionsModel;

    protected MastersModel $MastersModel;

    private $role_name = 'masters_banner';

    private $site_name = 'masters_banner';

    public $select_status = [
        'title' => '狀態切換',
        'type' => 'select',
        'name' => 'select_status',
        'placeholder' => '',
        'value' => 1,
        'required' => true,
        'id' => 'select_status',
        'option' => [
            1 => '上架活動',
            0 => '下架活動'
        ]
    ];

    public $editField = [
        [
            'title' => '選擇Banner類型',
            'type' => 'select',
            'name' => 'type',
            'placeholder' => '',
            'value' => 0,
            'required' => true,
            'id' => 'type',
            'option' => [
                0 => '內部連結',
                1 => '外部連結'
            ]
        ],
        [
            'title' => '外部連結',
            'type' => 'text',
            'name' => 'url',
            'placeholder' => '請輸入外部連結',
            'required' => true,
            'id' => 'url'
        ],
        [
            'title' => '科別',
            'type' => 'adminlte_select',
            'name' => 'division_id',
            'placeholder' => '',
            'value' => 0,
            'required' => false,
            'id' => 'division_id',
            'option' => [
                0 => '請選擇科別'
            ]
        ],
        [
            'title' => '院所',
            'type' => 'adminlte_select',
            'name' => 'institution_id',
            'placeholder' => '',
            'value' => 0,
            'required' => false,
            'id' => 'institution_id',
            'option' => [
                0 => '請選擇院所'
            ]
        ],
        [
            'title' => '醫師名稱',
            'type' => 'adminlte_select',
            'name' => 'master_id',
            'placeholder' => '',
            'value' => 0,
            'required' => false,
            'id' => 'master_id',
            'option' => [
                0 => '請選擇醫師'
            ]
        ],
        [
            'title' => '主視覺路徑',
            'type' => 'text',
            'name' => 'image',
            'placeholder' => '',
            'required' => true,
            'id' => 'image',
            'class' => 'col-md-8',
            'comment' => '建議圖片尺寸：2748 * 828 px,JPG',
            'image_gallery' => true
        ],
        [
            'title' => 'Mobile主視覺路徑',
            'type' => 'text',
            'name' => 'mobile_image',
            'placeholder' => '',
            'required' => true,
            'id' => 'mobile_image',
            'class' => 'col-md-8',
            'comment' => '建議圖片尺寸：1284 * 696 px,JPG',
            'image_gallery' => true
        ],
        [
            'title' => '活動上架時間',
            'type' => 'custome-date-start',
            'name' => 'published_at',
            'placeholder' => '請輸入活動上架時間',
            'required' => true,
            'id' => 'published_at',
            'class' => 'col-md-6'
        ],
        [
            'title' => '活動下架時間',
            'type' => 'custome-date-end',
            'name' => 'published_end',
            'placeholder' => '請輸入活動下架時間',
            'required' => true,
            'id' => 'published_end',
            'class' => 'col-md-6'
        ],
        [
            'title' => '活動狀態',
            'type' => 'select',
            'name' => 'status',
            'placeholder' => '',
            'value' => 1,
            'required' => true,
            'id' => 'status',
            'option' => [
                1 => '上架',
                0 => '下架'
            ]
        ],
    ];

    protected $validateRules = [
        'type' => 'required|numeric',
        'url' => 'required_if:type,1',
        'image' => ['required','regex:/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i'],
        'mobile_image' => ['required','regex:/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i'],
        'published_at' => ['required', 'date_format:Y-m-d H:i', 'date'],
        'published_end' => ['required', 'date_format:Y-m-d H:i', 'date', 'after:published_at'],
        'status' => 'required|numeric'
    ];

    protected $validateAttributes = [
        'type' => 'Banner類型',
        'url' => '外部連結',
        'image' => '主視覺路徑',
        'mobile_image' => 'Mobile主視覺路徑',
        'published_at' => '活動上架時間',
        'published_end' => '活動下架時間',
        'status' => '活動狀態'

    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'required_if' => '當Banner類型為外部連結時，:attribute為必填',
        'numeric' => ':attribute 必須為數字',
        'regex' => '請輸入正確:attribute',
        'date_format' => ':attribute 格式不符'
    ];

    /**
     * MastersBannerController constructor.
     * @param AdminRolesService $admin_roles
     * @param MastersBannerModel $MastersBannerModel
     * @param DivisionsModel $DivisionsModel
     * @param InstitutionsModel $InstitutionsModel
     * @param MastersModel $MastersModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        MastersBannerModel $MastersBannerModel,
        DivisionsModel $DivisionsModel,
        InstitutionsModel $InstitutionsModel,
        MastersModel $MastersModel
    ) {
        $this->admin_roles = $admin_roles;
        $this->MastersBannerModel = $MastersBannerModel;
        $this->DivisionsModel = $DivisionsModel;
        $this->InstitutionsModel = $InstitutionsModel;
        $this->MastersModel = $MastersModel;
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
        $cond = [];
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if ($keywords !== '') {
            $cond['keywords'] = $keywords;
        }
        $field = ['位置', '圖片', 'Mobile圖片', '上架時間', '下架時間', '狀態', '動作', '排序'];

        $selectStatus = isset($_GET['select_status']) ? $_GET['select_status'] : 1;

        $datas = $this->MastersBannerModel::orderBy('sort', 'asc')
                                    ->orderBy('id', 'desc')
                                    ->when($selectStatus == 1, function ($q) {
                                        $q->active();
                                    })
                                    ->when($selectStatus == 0, function ($q) {
                                        $q->inactive();
                                    })
                                    ->get();

        $select_status = $this->select_status;
        $editField = $this->editField;
        foreach ($editField as $k => $v) {
            if ($v['name'] == 'division_id') {
                $editField[$k]['option'] += $this->DivisionsModel::active()
                                    ->where('type', '!=', 2)
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray();
            }

            if ($v['name'] == 'institution_id') {
                $editField[$k]['option'] += $this->InstitutionsModel::active()
                                    ->get()
                                    ->pluck('nick_name', 'id')
                                    ->toArray();
            }

            if ($v['name'] == 'master_id') {
                $editField[$k]['option'] += $this->MastersModel::active()
                                        ->where('type', 1)
                                        ->get()
                                        ->pluck('name', 'id')
                                        ->toArray();
            }
        }

        return view('masters.banner.index', compact('role_name', 'site_name', 'search', 'keywords', 'field', 'no_id', 'selectStatus', 'datas', 'select_status', 'editField'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->request->remove('editId');
        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        if ($request->input('type') == 0) {
            $emptyDataNum = 0;

            $request->merge(['url' => null]);

            if (empty($request->input('division_id'))) {
                $request->request->remove('division_id');
                $emptyDataNum++;
            }
            if (empty($request->input('institution_id'))) {
                $request->request->remove('institution_id');
                $emptyDataNum++;
            }
            if (empty($request->input('master_id'))) {
                $request->request->remove('master_id');
                $emptyDataNum++;
            }
            if ($emptyDataNum == 3) {
                $errMsg = '科別、院所、醫師，請擇一選取';
                return response()->json(['errMsg' => $errMsg], 400);
            }
        }

        if ($request->input('type') == 1) {
            $pattern = '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i';
            if (!preg_match($pattern, $request->input('url'))) {
                $errMsg = '請輸入正確外部連結';
                return response()->json(['errMsg' => $errMsg], 400);
            }
            $request->merge(['division_id' => null]);
            $request->merge(['institution_id' => null]);
            $request->merge(['master_id' => null]);
        }

        $request->request->add([
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->MastersBannerModel->create($request->input());

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
        $role_name = $this->role_name;
        $site_name = $this->site_name;

        $data = $this->MastersBannerModel::where('id', $id)
                                        ->with(['institution', 'division', 'master'])
                                        ->firstOrFail();

        if ($data->type == 0) {
            $field = ['Banner類型', '科別', '院所', '醫師名稱', '主視覺路徑', 'Mobile主視覺路徑', '活動上架時間', '活動下架時間', '狀態'];
        }

        if ($data->type == 1) {
            $field = ['Banner類型', '外部連結', '主視覺路徑', 'Mobile主視覺路徑', '活動上架時間', '活動下架時間', '狀態'];
        }
        return view(
            'masters.banner.show',
            compact('role_name', 'site_name', 'field', 'data')
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = $this->MastersBannerModel->find($id);
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
        $request->request->remove('editId');
        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        if ($request->input('type') == 0) {
            $emptyDataNum = 0;

            $request->merge(['url' => null]);

            if (empty($request->input('division_id'))) {
                $request->merge(['division_id' => null]);
                $emptyDataNum++;
            }
            if (empty($request->input('institution_id'))) {
                $request->merge(['institution_id' => null]);
                $emptyDataNum++;
            }
            if (empty($request->input('master_id'))) {
                $request->merge(['master_id' => null]);
                $emptyDataNum++;
            }
            if ($emptyDataNum == 3) {
                $errMsg = '科別、院所、醫師，請擇一選取';
                return response()->json(['errMsg' => $errMsg], 400);
            }
        }

        if ($request->input('type') == 1) {
            $pattern = '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i';
            if (!preg_match($pattern, $request->input('url'))) {
                $errMsg = '請輸入正確外部連結';
                return response()->json(['errMsg' => $errMsg], 400);
            }
            $request->merge(['division_id' => null]);
            $request->merge(['institution_id' => null]);
            $request->merge(['master_id' => null]);
        }

        $request->request->add([
            'updated_user' => auth()->user()->name
        ]);

        $row = $this->MastersBannerModel->find($id);

        $row->update($request->input());

        return response()->json($request->input(), 200);
    }

    /**
     * saveSort.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSort(Request $request)
    {
        $position = 1;
        $data = $request->input('masters_banner');

        foreach ($data as $v) {
            $row = $this->MastersBannerModel->find($v['id']);
            $Update = [
                'sort' => $position,
                'updated_user' => auth()->user()->name
            ];
            $row->update($Update);
            $position++;
        }
        return response()->json($request->input(), 200);
    }

    /**
     * getMaster.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMaster($division_id, $institution_id)
    {
        $result = $this->MastersModel::when(!empty($division_id), function ($query) use ($division_id) {
                        $query->whereHas('divisions', function ($q) use ($division_id) {
                            $q->where('division_id', $division_id);
                        });
        })
                    ->when(!empty($institution_id), function ($query) use ($institution_id) {
                        $query->where('institution_id', $institution_id);
                    })
                    ->where('type', 1)
                    ->active()
                    ->get();

        return response()->json($result);
    }
}
