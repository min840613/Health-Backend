<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\Categories\MainCategoriesModel;
use App\Models\Categories\SubCategoriesModel;
use Session;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\SubCategoriesRepository;

class SubCategoriesController extends Controller
{
    const STATUS_ARRAY = [
        '0' => '下架',
        '1' => '上架'
    ];

    protected MainCategoriesModel $MainCategoriesModel;

    protected SubCategoriesModel $SubCategoriesModel;

    protected SubCategoriesRepository $SubCategoriesRepository;

    private $role_name = 'main_categories';

    private $site_name = 'sub_categories';

    protected $validateRules = [
        'name' => 'required|max:50',
        'en_name' => 'required|max:15|regex:/^[a-zA-Z0-9]+$/',
        'meta_title' => 'required|max:50',
        'description' => 'required|max:160'
    ];

    protected $validateAttributes = [
        'name' => '子分類名稱',
        'en_name' => '英文名稱',
        'meta_title' => 'Meta Title',
        'description' => 'Description',
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'max' => ':attribute 最多:max個字',
        'regex' => ':attribute 請輸入英數字'
    ];

    public $editField = [
        [
            'title' => '子分類名稱',
            'type' => 'text',
            'name' => 'name',
            'placeholder' => '請輸入子分類名稱',
            'required' => true,
            'id' => 'name'
        ],
        [
            'title' => '子分類英文名稱',
            'type' => 'text',
            'name' => 'en_name',
            'placeholder' => '請輸入子分類英文名稱',
            'required' => true,
            'id' => 'en_name',
            'hint' => '不得修改'
        ],
        [
            'title' => 'Meta Title',
            'type' => 'textarea',
            'row' => '2',
            'name' => 'meta_title',
            'placeholder' => '請輸入 Meta Title',
            'required' => true,
            'id' => 'meta_title'
        ],
        [
            'title' => 'Meta Description',
            'type' => 'textarea',
            'row' => '3',
            'name' => 'description',
            'placeholder' => '請輸入 Meta Description',
            'required' => true,
            'id' => 'description'
        ],
        [
            'title' => '上下架',
            'type' => 'select',
            'name' => 'status',
            'id' => 'status',
            'option' => self::STATUS_ARRAY
        ],
    ];

    /**
     * SubCategoriesController constructor.
     * @param AdminRolesService $admin_roles
     * @param MainCategoriesModel $MainCategoriesModel
     * @param SubCategoriesModel $SubCategoriesModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        MainCategoriesModel $MainCategoriesModel,
        SubCategoriesModel $SubCategoriesModel,
        SubCategoriesRepository $SubCategoriesRepository
    ) {
        $this->admin_roles = $admin_roles;
        $this->MainCategoriesModel = $MainCategoriesModel;
        $this->SubCategoriesModel = $SubCategoriesModel;
        $this->SubCategoriesRepository = $SubCategoriesRepository;
        $this->middleware = $this->admin_roles->AdminRoles($this->role_name)->middleware;
        $this->function_role = $this->admin_roles->AdminRoles($this->role_name)->function_role;
        $this->menu_list = $this->admin_roles->AdminRoles($this->role_name)->menu_list;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($main_id)
    {
        $role_name = $this->role_name;
        $site_name = $this->site_name;
        $page_limit = 200;
        $no_id = true;
        $search = false;
        $cond = [];
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if ($keywords !== '') {
            $cond['keywords'] = $keywords;
        }
        $field = ['#', '子分類名', '最後修改時間', '狀態'];

        $main_categories = $this->MainCategoriesModel->where('categories_id', $main_id)->first();

        if (!$main_categories) {
            return redirect()->route('main_categories.index')->withErrors('此主分類不存在，無法進入子分類管理');
        }

        $datas = $this->SubCategoriesModel->where(function ($q) use ($cond) {
            foreach ($cond as $key => $value) {
                $q->where('name', 'like', '%' . $value . '%');
            }
        })
                    ->where('categories_id', $main_id)
                    ->orderBy('sort', 'asc')
                    ->paginate($page_limit)
                    ->through(function ($row) {
                        $row->status = self::STATUS_ARRAY[$row->status];
                        return $row;
                    })
                    ->appends($cond);

        $editField = $this->editField;
        $has_sort = true;
        return view('categories.sub_categories.index', compact('role_name', 'site_name', 'search', 'keywords', 'field', 'no_id', 'main_categories', 'datas', 'editField', 'has_sort'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($main_id)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $main_id)
    {
        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        // 判斷子分類英文名稱是否已存在
        if (
            $this->SubCategoriesModel->where('en_name', $request->input('en_name'))
                                    ->where('categories_id', $main_id)
                                    ->first()
        ) {
            return response()->json(['errMsg' => '此主分類下的子分類已有此英文名稱'], 400);
        }

        $processedName = special_char_half_to_full($request->input('name'));

        $request->merge(['name' => $processedName]);

        $request->request->add([
            'categories_id' => $main_id,
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->SubCategoriesModel->create($request->input());

        return response()->json($request->input(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($main_id, $sub_id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($main_id, $sub_id)
    {
        $result = $this->SubCategoriesModel->find($sub_id);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $main_id, $sub_id)
    {
        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);


        // 當子分類要下架要時，需判斷是否有所屬文章，沒有才允許下架
        if ($request->input('status') == '0') {
            // 查看子分類底下的文章數量
            $subCategoriesData = $this->SubCategoriesRepository->getArticlesCount($sub_id);

            if (!is_null($subCategoriesData) && $subCategoriesData->articles_count > 0) {
                return response()->json(['errMsg' => '此子分類下有文章，無法下架'], 400);
            }
        }

        $row = $this->SubCategoriesModel->find($sub_id);

        if ($row->en_name) {
            $request->request->remove('en_name');
        } else {
            // 判斷子分類英文名稱是否已存在
            if (
                $this->SubCategoriesModel->where('en_name', $request->input('en_name'))
                    ->where('categories_id', $main_id)
                    ->first()
            ) {
                return response()->json(['errMsg' => '此主分類下的子分類已有此英文名稱'], 400);
            }
        }

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
    public function destroy($main_id, $sub_id)
    {
        //
    }

    /**
     * @param string $mainCategoryId
     * @return Response
     * @throws \Exception
     */
    public function getCategoryByMain(string $mainCategoryId): Response
    {
        $main = $this->MainCategoriesModel->find($mainCategoryId);

        if ($main === null) {
            throw new \Exception('model notfound.');
        }

        $result = $main->subCategories->where('status', 1)->values();

        return response()->json($result);
    }

    /**
     * 儲存排序
     */
    public function sort(Request $request)
    {
        $ids = $request->input('ids');
        foreach ($ids as $sort => $sub_category_id) {
            $data = $this->SubCategoriesModel->find($sub_category_id);
            $data->update(['sort' => $sort]);
        }
        return response()->json([], 200);
    }
}
