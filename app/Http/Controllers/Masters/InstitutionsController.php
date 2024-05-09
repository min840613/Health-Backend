<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstitutionsIndex;
use App\Http\Requests\InstitutionsSaveSort;
use App\Http\Requests\InstitutionsStore;
use App\Http\Requests\InstitutionsUpdate;
use App\Models\Masters\InstitutionsModel;
use App\Services\InstitutionsService;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InstitutionsController
 * @package App\Http\Controllers\Masters
 */
class InstitutionsController extends Controller
{
    /** @var string */
    private $role_name = 'institutions';

    /** @var */
    private $settings;

    /** @var InstitutionsService  */
    private InstitutionsService $service;

    /**
     * InstitutionsController constructor.
     * @param InstitutionsService $service
     */
    public function __construct(InstitutionsService $service)
    {
        $this->settings = config('settings.views.institutions');
        $this->service = $service;
    }

    /**
     * 列表功能
     * @param InstitutionsIndex $request
     * @return View
     */
    public function index(InstitutionsIndex $request): View
    {
        $data = $request->validated();

        $institutions = $this->service->lists($data);

        $this->settings['edit_field'][] = [
            'title' => '',
            'type' => 'custom',
            'custom' => 'masters.institutions.custom_checkbox',
            'name' => 'custom',
        ];

        $this->settings['edit_field'][] = [
            'title' => '上下架狀態',
            'type' => 'custom',
            'required' => true,
            'custom' => 'masters.institutions.custom',
            'name' => 'custom',
        ];

        return view('masters.institutions.index', [
            'role_name' => $this->role_name,
            'editField' => $this->settings['edit_field'],
            'field' => ['編號', '院所名稱', '簡稱', '建立時間', '更新時間', '狀態', '編輯', '排序'],
            'datas' => $institutions,
        ]);
    }

    /**
     * 新增資料
     *
     * @param InstitutionsStore $request
     * @return Response
     */
    public function store(InstitutionsStore $request): Response
    {
        $data = $request->validated();

        $errors = $this->service->validateUniqueEnName($request->all(), null);

        if (!empty($errors)) {
            return response()->json(['errMsg' => $errors['status']], 400);
        }

        $data['is_centre'] = $data['is_centre'] ?? 0;

        $this->service->create($data, auth()->user()->name);

        return response()->json($request->input(), 200);
    }

    /**
     * 帶出要編輯的資料
     *
     * @param InstitutionsModel $institution
     * @return Response
     */
    public function edit(InstitutionsModel $institution)
    {
        return response()->json($institution);
    }

    /**
     * 更新資料
     *
     * @param InstitutionsUpdate $request
     * @param int $id
     * @return Response
     */
    public function update(InstitutionsUpdate $request, $id)
    {
        $data = $request->validated();
        $data['is_centre'] = isset($data['is_centre']) ? $data['is_centre'] : 0;

        $errors = $this->service->validate($id, $request->all());

        if (!empty($errors)) {
            return response()->json(['errMsg' => $errors['status']], 400);
        }

        $data['id'] = $id;

        $this->service->update($data, auth()->user()->name);

        return response()->json($request->input(), 200);
    }

    /**
     * 儲存排序
     *
     * @param InstitutionsSaveSort $request
     * @return Response
     */
    public function saveSort(InstitutionsSaveSort $request): Response
    {
        $data = $request->validated();

        $this->service->saveSort($data['masters_institutions']);

        return response()->success([]);
    }
}
