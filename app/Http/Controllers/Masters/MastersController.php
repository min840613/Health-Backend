<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\DivisionsModel;
use App\Models\Masters\InstitutionsModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\Masters\MastersModel;
use Carbon\Carbon;
use Session;

class MastersController extends Controller
{
    protected MastersModel $MastersModel;
    private $role_name = 'masters';
    private $site_name = 'masters';
    protected $validateRules = [
        'name' => 'required|max:50',
        'en_name' => 'required|alpha_num:ascii|max:50',
        'status' => 'required',
        'type' => 'required',
        'description' => 'nullable|max:30',
        'institution_id' => 'required_if:type,1|min:1',
        'title' => 'required_if:type,1',
        'experiences' => 'required_if:type,1|array',
        'experiences.*' => 'required_if:type,1',
        'expertise' => 'required_if:type,1|array',
        'expertise.*' => 'required_if:type,1',
        'divisions' => 'required_if:type,1|array',
        'divisions.*' => 'required_if:type,1',
        'is_contracted' => 'required|boolean',
        'content_image' => 'required_if:type,1',
    ];
    protected $validateAttributes = [
        'name' => '名稱',
        'en_name' => '英文名稱',
        'status' => '狀態',
        'type' => '類型',
        'description' => '首頁簡介',
        'institution_id' => '目前所屬醫院',
        'title' => '目前職稱',
        'experiences.*' => '經歷',
        'expertise.*' => '專長',
        'divisions.*' => '科別',
        'is_contracted' => '簽約醫師',
        'content_image' => '醫師內容圖片',
    ];
    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'max' => ':attribute 最多:max 個字',
        'alpha_num' => ':attribute 只能是英文及數字',
        'required_if' => '若類型為「醫師」時，「:attribute」為必填',
        'institution_id.min' => '若類型為「醫師」時，「:attribute」為必填',
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
            'title' => '英文名稱',
            'type' => 'text',
            'name' => 'en_name',
            'placeholder' => '請輸入英文名稱',
            'required' => TRUE,
            'id' => 'en_name',
            'class' => 'col-10'
        ],
        [
            'title' => '類型',
            'type' => 'select',
            'name' => 'type',
            'placeholder' => '',
            'value' => 1,
            'required' => TRUE,
            'id' => 'type',
            'option' => [
                1 => '醫師',
                2 => '專家',
                3 => '營養師'
            ]
        ],
        [
            'title' => '醫師列表圖片',
            'type' => 'text',
            'name' => 'image',
            'placeholder' => '',
            'required' => FALSE,
            'id' => 'image'
        ],
        [
            'title' => '醫師內頁圖片',
            'type' => 'text',
            'name' => 'content_image',
            'placeholder' => '',
            'required' => TRUE,
            'id' => 'content_image',
            'main_class' => 'type_doctor',
        ],
        [
            'title' => '首頁簡介',
            'type' => 'textarea',
            'name' => 'description',
            'placeholder' => '請輸入簡介',
            'required' => FALSE,
            'id' => 'description',
            'hint' => '不得超過30字'
        ],
        [
            'title' => '目前所屬醫院',
            'type' => 'select2',
            'name' => 'institution_id',
            'placeholder' => '請輸入目前所屬醫院',
            'required' => TRUE,
            'id' => 'institution_id',
            'option' => [],
            'main_class' => 'type_doctor',
        ],
        [
            'title' => '目前職稱',
            'type' => 'text',
            'name' => 'title',
            'placeholder' => '請輸入目前職稱',
            'required' => TRUE,
            'id' => 'title',
            'main_class' => 'type_doctor',
        ],
        [
            'title' => '科別',
            'type' => 'dynamic_text',
            'name' => 'divisions',
            'placeholder' => '請輸入科別',
            'required' => TRUE,
            'id' => 'divisions',
            'option' => [],
            'elements' => '<div class="form-group"><div class="input-group">'.
                '<select class="form-control select2 col-3 dynamic_text" name="divisions[]" style="width:250px"><:options></select>'.
                '<input name="division_remark[]" class="form-control dynamic_job"></div></div>',
            'main_class' => 'type_doctor',
        ],
        [
            'title' => '經歷(最多5~8項)',
            'type' => 'dynamic_text',
            'name' => 'experiences',
            'placeholder' => '請輸入經歷',
            'required' => TRUE,
            'id' => 'experiences',
            'max_count' => 8,
            'elements' => '<div class="form-group"><div class="input-group"><input name="experiences[]" class="form-control dynamic_text">'.
                '<select class="form-control col-3 dynamic_job" name="is_current_job[]"><option value="0">非現職</option><option value="1">現職</option></select></div></div>',
            'main_class' => 'type_doctor',
        ],
        [
            'title' => '專長',
            'type' => 'dynamic_text',
            'name' => 'expertise',
            'placeholder' => '請輸入專長',
            'required' => TRUE,
            'id' => 'expertise',
            'elements' => '<div class="form-group"><div class="input-group"><input name="expertise[]" class="form-control dynamic_text"></div></div>',
            'main_class' => 'type_doctor',
        ],
        [
            'title' => '簽約醫師',
            'type' => 'select',
            'name' => 'is_contracted',
            'placeholder' => '',
            'value' => 1,
            'required' => TRUE,
            'id' => 'is_contracted',
            'option' => [
                1 => '是',
                0 => '否',
            ],
            'main_class' => 'type_doctor',
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
        ],
    ];
    public $filters = [
        'master_status' => [
            1 => '上架',
            0 => '下架'
        ],
        'master_type' => [
            1 => '醫師',
            2 => '專家',
            3 => '營養師'
        ]
    ];

    /**
     * MastersController constructor.
     * @param AdminRolesService $admin_roles
     * @param MastersModel $MastersModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        MastersModel $MastersModel
    )
    {
        $this->admin_roles = $admin_roles;
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
        $queryParams = request()->all();
        $role_name = $this->role_name;
        $site_name = $this->site_name;
        $page_limit = 10;
        $no_id = true;
        $search = false;
        $field = array('#','名稱', '英文名稱', '簽約醫師', '類型', '狀態');

        $datas = $this->MastersModel::query();
        if (!empty($queryParams['master_name'])) {
            $datas = $datas->where('name', 'like', '%' . $queryParams['master_name'] . '%');
        }
        if (!empty($queryParams['master_en_name'])) {
            $datas = $datas->where('en_name', 'like', '%' . $queryParams['master_en_name'] . '%');
        }
        if (!empty($queryParams['master_type'])) {
            $datas = $datas->where('type', '=', $queryParams['master_type']);
        }
        if (isset($queryParams['master_status'])) {
            $datas = $datas->where('status', '=', $queryParams['master_status']);
        }
        if (!empty($queryParams['created_date_start'])) {
            $datas = $datas->whereBetween('created_at', [(new Carbon($queryParams['created_date_start']))->startOfDay(), (new Carbon($queryParams['created_date_end']))->endOfDay()]);
        }
        if (!empty($queryParams['master_division'])) {
            $datas = $datas->whereHas('divisions', function ($query) use ($queryParams) {
                $query->where('division_id', $queryParams['master_division']);
            });
        }
        if (!empty($queryParams['master_institution'])) {
            $datas = $datas->where('institution_id', $queryParams['master_institution']);
        }

        $query = $datas->orderByDesc('created_at')->orderBy('en_name');

        $countQuery = clone $query;
        $counts = $countQuery->select(['id', 'type'])->get();

        $datas = $query->paginate($page_limit)->appends($queryParams);

        $this->filters['master_division'] = DivisionsModel::where('type', 1)->orderBy('sort')->get()->pluck('name', 'id');
        $this->filters['master_institution'] = InstitutionsModel::orderBy('sort')->get()->pluck('nick_name', 'id');

        $editField = $this->editField;
        $editField = collect($editField)->transform(function ($field) {
            if ($field['id'] === 'institution_id') {
                // 保留 key
                $field['option'] = $field['option'] + InstitutionsModel::where('status', 1)->get()->sortBy('sort')->pluck('nick_name', 'id')->toArray();
            }
            if ($field['id'] === 'divisions') {
                // 保留 key
                $elements = DivisionsModel::where('type', 1)->where('status', 1)->get()->sortBy('sort')->map(function ($division) {
                    return "<option value='{$division['id']}'>{$division['name']}</option>";
                })->implode('');

                $field['elements'] = str_replace('<:options>', "<option value='0'>請選擇</option>".$elements, $field['elements']);
            }
            return $field;
        })->toArray();
        $filters = $this->filters;

        return view('masters.masters.index', compact('role_name','site_name','search','field','no_id','datas','editField', 'filters', 'counts'));
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

        $users = [
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name,
        ];

        $request->request->add($users);

        \DB::transaction(function () use ($request, $users) {
            $master = new $this->MastersModel();
            $master->fill($request->input());
            $master->save();

            collect($request->experiences)->filter()->values()->each(function ($experience, $index) use ($master, $users, $request) {
                $master->experiences()->create(array_merge(['name' => $experience, 'is_current_job' => $request->is_current_job[$index] ?? false], $users));
            });

            collect($request->expertise)->filter()->values()->each(function ($expertise) use ($master, $users) {
                $master->expertise()->create(array_merge(['name' => $expertise], $users));
            });

            collect($request->divisions)->values()->each(function ($division, $index) use ($master, $users, $request) {
                $master->divisions()->create(array_merge(['division_id' => $division, 'description' => $request->division_remark[$index] ?? ''], $users));
            });
        });

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
     * @param int $id
     * @return JsonResponse
     */
    public function edit($id): JsonResponse
    {
        $result = $this->MastersModel->find($id);

        $result->load(['experiences', 'expertise', 'divisions']);

        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        $row = $this->MastersModel->find($id);

        $processedName = special_char_half_to_full($request->input('name'));

        $request->merge(['name' => $processedName]);

        $request->request->add([
            'updated_user' => auth()->user()->name
        ]);

        $data = $request->all();

        if ($data['type'] != 1) {
            $data['institution_id'] = 0;
            $data['content_image'] = null;
            $data['title'] = null;
            $data['experiences'] = [];
            $data['expertise'] = [];
            $data['divisions'] = [];
            $data['is_current_job'] = [];
            $data['is_contracted'] = 0;
        }

        \DB::transaction(function () use ($row, $data) {
            $users = [
                'created_user' => auth()->user()->name,
                'updated_user' => auth()->user()->name,
            ];

            $row->experiences()->delete();
            foreach (array_filter($data['experiences']) as $index => $experiences) {
                $row->experiences()->create(array_merge(['name' => $experiences, 'is_current_job' => $data['is_current_job'][$index]], $users));
            }

            $row->expertise()->delete();
            foreach (array_filter($data['expertise']) as $expertise) {
                $row->expertise()->create(array_merge(['name' => $expertise], $users));
            }

            $row->divisions()->delete();
            foreach (array_filter($data['divisions']) as $index => $division) {
                $row->divisions()->create(array_merge(['division_id' => $division, 'description' => $data['division_remark'][$index]], $users));
            }

            $row->update($data);
        });

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
        MastersModel::destroy(1);
        // MastersModel::find($id)->delete();
        return redirect()->route($this->site_name . '.index')
                ->with('success','刪除成功');
    }
}
