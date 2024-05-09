<?php

namespace App\Http\Controllers\Encyclopedia;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrgansIndex;
use App\Http\Requests\OrgansStore;
use App\Http\Requests\OrgansUpdate;
use App\Http\Requests\OrgansSaveSort;
use App\Services\OrgansService;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Rules\SvgSizeRule;
use App\Repositories\OrgansRepository;


class OrgansController extends Controller
{
    /** @var string */
    private $role_name = 'organs';

    /** @var string */
    private $site_name = 'organs';

    /** @var */
    private $settings;

    private OrgansService $service;
    private OrgansRepository $repository;

    /**
     * OrgansController constructor.
     * @param OrgansService $service
     * @param OrgansRepository $repository
     */
    public function __construct(OrgansService $service, OrgansRepository $repository)
    {
        $this->settings = config('settings.views.organs');
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * 列表功能
     * 
     * @param OrgansIndex $request
     * @return View
     */
    public function index(OrgansIndex $request): View
    {

        $data = $request->validated();

        $organs = $this->service->findOrgans($data);

        $custom = [
            'title' => '請上傳相關檔案',
            'type' => 'custom',
            'required' => true,
            'custom' => 'encyclopedia.organs.custom',
            'name' => 'custom',


        ];
        $this->settings['edit_field'][] = $custom;

        return view('encyclopedia.organs.index', [
            'role_name' => $this->role_name,
            'editField' => $this->settings['edit_field'],
            'field' => ['編號', '身體部位', '器官與組織', 'Icon', '建立時間', '更新時間', '狀態', '編輯', '疾病子分類', '排序'],
            'filters' => ['bodies' => $this->service->filterByBodies()],
            'datas' => $organs,
        ]);
    }

    /**
     * 帶出要編輯的資料
     * 
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return response()->json($this->repository->find($id));
    }

    /**
     * 更新資料
     * 
     * @param OrgansUpdate $request
     * @param int $id
     * @return Response
     */
    public function update(OrgansUpdate $request, $id)
    {

        $uploadArray = [];

        // 如果有上傳的檔案，重新上傳，再將網址拿去更新資料庫內
        if ($request->hasFile('icon')) {
            $this->validate($request, ['icon' =>  [new SvgSizeRule($request->file('icon')->path(), 30, 30)]]);
            $uploadArray[] = 'icon';
        }
        if ($request->hasFile('icon_android')) {
            $uploadArray[] = 'icon_android';
        }
        if ($request->hasFile('icon_ios')) {
            $uploadArray[] = 'icon_ios';
        }

        foreach ($uploadArray as $key) {
            $filePath = config('constants.s3.path') . date('Y') . '/' . date('m') . '/';
            $fileExtension = $request->file($key)->getClientOriginalExtension();
            $fileName = date('YmdHis') . '-' . Str::random(8) . '.' . $fileExtension;
            $s3Path = $filePath . $fileName;
            Storage::disk('s3')->put($s3Path, file_get_contents($request->file($key)->path()));
            $request->request->add([$key.'_full_url' => config('constants.cdn.url') . $s3Path]);
        }

        $processedName = special_char_half_to_full($request->input('name'));

        $request->merge(['name' => $processedName]);

        $request->request->add([
            'status' => $request->input('status'),
            'updated_user' => auth()->user()->name,
            'id' => $id,
        ]);


        $this->service->update($request->input());

        return response()->json($request->input(), 200);
    }

    /**
     * 新增資料
     * 
     * @param OrgansStore $request
     * @return Response
     */
    public function store(OrgansStore $request) 
    {

        // svg won't vaildate size, see Illuminate\Validation\Concerns\ValidatesAttributes@validateDimensions
        $this->validate($request, ['icon' =>  [new SvgSizeRule($request->file('icon')->path(), 30, 30)]]);

        $uploadArray = ['icon', 'icon_android', 'icon_ios'];

        foreach ($uploadArray as $key) {
            $filePath = config('constants.s3.path') . date('Y') . '/' . date('m') . '/';
            $fileExtension = $request->file($key)->getClientOriginalExtension();
            $fileName = date('YmdHis') . '-' . Str::random(8) . '.' . $fileExtension;
            $s3Path = $filePath . $fileName;
            Storage::disk('s3')->put($s3Path, file_get_contents($request->file($key)->path()));
            $request->request->add([$key.'_full_url' => config('constants.cdn.url') . $s3Path]);
        }

        $processedName = special_char_half_to_full($request->input('name'));

        $request->merge(['name' => $processedName]);

        $request->request->add([
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name,
        ]);

        $this->service->create($request->input());

        return response()->json($request->input(), 200);
    }

    /**
     * 儲存排序
     * 
     * @param OrgansSaveSort $request
     * @return Response
     */
    public function saveSort(OrgansSaveSort $request): Response
    {
        $data = $request->validated();

        $this->service->saveSort($data['encyclopedia_organs']);

        return response()->success([]);
    }
}
