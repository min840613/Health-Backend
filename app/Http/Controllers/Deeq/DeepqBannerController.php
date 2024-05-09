<?php

namespace App\Http\Controllers\Deeq;

use App\Http\Controllers\Controller;
use App\Models\Deepq\DeepqBannerModel;
use App\Services\AdminRolesService;
use Illuminate\Http\Request;
use App\Helpers\ImageHelper;
use App\Http\Requests\DeepqBannerStore;
use App\Http\Requests\DeepqBannerUpdate;
use Carbon\Carbon;

class DeepqBannerController extends Controller
{
    protected DeepqBannerModel $DeepqBannerModel;

    private $role_name = 'deepq_banners';

    private $site_name = 'deepq_banners';

    public $select_type = [
        'title' => '狀態切換',
        'type' => 'select',
        'name' => 'select_type',
        'placeholder' => '',
        'value' => 1,
        'required' => true,
        'id' => 'select_type',
        'option' => [
            1 => '上架活動',
            0 => '下架活動'
        ]
    ];

    public $searchField = [
        [
            'title' => '活動名稱',
            'type' => 'text',
            'name' => 'search_title',
            'placeholder' => '請輸入活動名稱',
            'required' => false,
            'id' => 'search_title'
        ],
        [
            'title' => '活動區間',
            'type' => 'datetime',
            'name' => 'datetime',
            'elements' =>
            [
                'start' => [
                    'title' => '',
                    'type' => 'datetime',
                    'name' => 'search_start',
                    'placeholder' => '請輸入活動上架時間',
                    'required' => false,
                    'id' => 'search_start',
                    'class' => 'col-md-12'
                ],
                'end' => [
                    'title' => '',
                    'type' => 'datetime',
                    'name' => 'search_end',
                    'placeholder' => '請輸入活動下架時間',
                    'required' => false,
                    'id' => 'search_end',
                    'class' => 'col-md-12',
                ]
            ]
        ],
    ];

    public $editField = [
        [
            'title' => '活動名稱',
            'type' => 'text',
            'name' => 'title',
            'placeholder' => '此欄位文字不會出現在用戶端',
            'required' => true,
            'id' => 'title'
        ],
        [
            'title' => '主視覺路徑',
            'type' => 'text',
            'name' => 'image',
            'placeholder' => '請輸入主視覺路徑，建議圖片尺寸：375 * 95',
            'required' => true,
            'id' => 'image',
            'class' => 'col-md-8',
            'image_gallery' => true
        ],
        [
            'title' => '活動上架時間',
            'type' => 'custome-date-start-common',
            'name' => 'start',
            'placeholder' => '請輸入活動上架時間',
            'required' => true,
            'id' => 'start',
            'class' => 'col-md-6'
        ],
        [
            'title' => '活動下架時間',
            'type' => 'custome-date-end-common',
            'name' => 'end',
            'placeholder' => '請輸入活動下架時間',
            'required' => true,
            'id' => 'end',
            'class' => 'col-md-6'
        ],
        [
            'title' => '活動狀態',
            'type' => 'select',
            'name' => 'status',
            'placeholder' => '',
            'value' => 0,
            'required' => true,
            'id' => 'status',
            'option' => [
                1 => '上架',
                0 => '下架'
            ]
        ],
    ];

    /**
     * DeepqBannerController constructor.
     * @param AdminRolesService $admin_roles
     * @param DeepqBannerModel $DeepqBannerModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        DeepqBannerModel $DeepqBannerModel
    ) {
        $this->admin_roles = $admin_roles;
        $this->DeepqBannerModel = $DeepqBannerModel;
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
        $search = false;
        $cond = [];
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if ($keywords !== '') {
            $cond['keywords'] = $keywords;
        }

        (int)$select_type = isset($_GET['select_type']) ? $_GET['select_type'] : 1;

        $defaultData = null;

        if ($select_type) {
            $field = ['位置', '活動名稱', '活動圖', '活動上架時間', '活動下架時間', '狀態', '動作', '排序'];
            $defaultData = $this->DeepqBannerModel::find(1);
            $datas = $this->DeepqBannerModel
                            ->where('status', $select_type)
                            ->where('end', '>', date('Y-m-d H:i:s'))
                            ->where('id', '!=', 1)
                            ->orderBy('sort', 'ASC')
                            ->orderBy('id', 'DESC')
                            ->get();
        } else {
            $field = ['位置', '活動名稱', '活動圖', '活動上架時間', '活動下架時間'];

            $cond['select_type'] = $select_type;

            $search_title = isset($_GET['search_title']) ? $_GET['search_title'] : '';
            $search_title = trim(urldecode($search_title));
            if ($search_title !== '') {
                $cond['search_title'] = $search_title;
            }

            $search_start = isset($_GET['search_start']) ? date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $_GET['search_start']))) : date('Y-m-d H:i:s', strtotime('-7 day'));
            $search_start = trim(urldecode($search_start));
            if ($search_start !== '') {
                $cond['search_start'] = $search_start;
            }

            $search_end = isset($_GET['search_end']) ? date('Y-m-d H:i:s', strtotime('+1 day', strtotime(str_replace("T", " ", $_GET['search_end'])))) : date('Y-m-d H:i:s', strtotime('+1 day'));
            $search_end = trim(urldecode($search_end));
            if ($search_end !== '') {
                $cond['search_end'] = $search_end;
            }

            $datas = $this->DeepqBannerModel
                            ->where(function ($q) use ($cond) {
                                if (isset($cond['search_title'])) {
                                    $q->where('title', 'like', '%' . $cond['search_title'] . '%');
                                }
                                if (isset($cond['search_start'])) {
                                    $q->where('start', '>=', $cond['search_start']);
                                }
                                if (isset($cond['search_end'])) {
                                    $q->where('start', '<', $cond['search_end']);
                                }
                            })
                            ->whereNot(function ($query) {
                                $query ->where('status', '=', 1)
                                        ->where('end', '>', date('Y-m-d H:i:s'));
                            })
                            ->where('id', '!=', 1)
                            ->orderBy('start', 'DESC')
                            ->orderBy('sort', 'ASC')
                            ->orderBy('id', 'DESC')
                            ->paginate($page_limit)
                            ->appends($cond);
        }

        $select_type = $this->select_type;
        $searchField = $this->searchField;
        $editField = $this->editField;

        return view('deepq.banners.index', compact('role_name', 'site_name', 'search', 'keywords', 'field', 'no_id', 'datas', 'select_type', 'searchField', 'editField', 'defaultData'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request\DeepqBannerStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeepqBannerStore $request)
    {
        $processedTitle = special_char_half_to_full($request->input('title'));

        $request->merge(['title' => $processedTitle]);

        $data = $request->validated();

        if ($data['status'] == '1' && strtotime($data['start']) < time()) {
            $errMsg = '活動上架時間需大於當時時間';
            return response()->json(['errMsg' => $errMsg], 400);
        }

        //檢查主圖寬高是否跟所設定的寬高不同
        $errMsg = ImageHelper::judgeImageSize($data['image'], 375, 95);
        if ($errMsg) {
            return response()->json(['errMsg' => $errMsg], 400);
        }

        $errMsg = $this->judgeActivityBanner($data['start'], $data['end']);
        if ($errMsg) {
            return response()->json(['errMsg' => $errMsg], 400);
        }

        $data['created_user'] = auth()->user()->name;
        $data['updated_user'] = auth()->user()->name;

        $banners = $this->DeepqBannerModel::all();

        if ($banners->isEmpty()) {
            $data['start'] = Carbon::now()->subYears(50)->format('Y-m-d H:i');
            $data['end'] = Carbon::now()->addYears(10)->format('Y-m-d H:i');

            if ($data['status'] == 0) {
                return response()->json(['errMsg' => '預設Banner不能為下架狀態'], 400);
            }
        }

        $this->DeepqBannerModel->create($data);

        return response()->json($data, 200);
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

        $field = ['活動名稱', '活動圖', '活動上架時間', '活動下架時間', '狀態'];

        $data = $this->DeepqBannerModel::where('id', $id)
                                    ->firstOrFail();

        return view(
            'deepq.banners.show',
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
        $result = $this->DeepqBannerModel->find($id);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request\DeepqBannerUpdate  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DeepqBannerUpdate $request, $id)
    {
        $processedTitle = special_char_half_to_full($request->input('title'));

        $request->merge(['title' => $processedTitle]);

        $data = $request->validated();

        if ($id == 1) {
            if ($data['status'] == 0) {
                return response()->json(['errMsg' => '預設Banner不能為下架狀態'], 400);
            }
        } else {
            $errMsg = $this->judgeActivityBanner($data['start'], $data['end'], $id);
            if ($errMsg) {
                return response()->json(['errMsg' => $errMsg], 400);
            }
        }



        //檢查主圖寬高是否跟所設定的寬高不同
        $banner_entity = $this->DeepqBannerModel->find($id);
        if ($banner_entity) {
            $errMsg = ImageHelper::judgeImageSize($data['image'], 375, 95);
            if ($errMsg) {
                return response()->json(['errMsg' => $errMsg], 400);
            }

            if ($banner_entity['status'] && strtotime($banner_entity['start']) < time() && strtotime($banner_entity['start']) != strtotime($data['start'])) {
                $errMsg = '活動上架則不可修改上架時間';
                return response()->json(['errMsg' => $errMsg], 400);
            }
        }

        if ($id == 1) {
            unset($data['start'], $data['end'], $data['status']);
        }

        $data['updated_user'] = auth()->user()->name;

        $banner_entity->update($data);

        return response()->json($data, 200);
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

    public function saveSort(Request $request)
    {
        $position = 1;
        $bannersRequest = $request->input('deepq_banners');
        $sortId = explode(',', $request->input('sortId'));

        foreach ($sortId as $v) {
            $bannersId = $bannersRequest[$v]['id'];
            $row = $this->DeepqBannerModel->find($bannersId);
            $bannersUpdata = [
                'sort' => $position,
                'updated_user' => auth()->user()->name
            ];
            $row->update($bannersUpdata);
            $position++;
        }
        return response()->json($request->input(), 200);
    }

    private function judgeActivityBanner($start, $end, $id = 0)
    {
        if ($id == 1) {
            return  '';
        }

        $errMsg = '';

        $banner = $this->DeepqBannerModel::where('id', '!=', 1)
                                        ->when($id, function ($q) use ($id) {
                                            $q->where('id', '!=', $id);
                                        })
                                        ->where(function ($q) use ($start, $end) {
                                            $q->whereBetween('start', [$start, $end])
                                                  ->orWhereBetween('end', [$start, $end]);
                                        })
                                        ->where('status', 1)
                                        ->get();

        if ($banner->first()) {
            $errMsg = '此時間區間已有活動';
        }

        return $errMsg;
    }
}
