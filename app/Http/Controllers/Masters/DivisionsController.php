<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Http\Requests\DivisionsSaveSort;
use App\Http\Requests\DivisionsStore;
use App\Http\Requests\DivisionsUpdate;
use App\Models\Masters\DivisionsModel;
use App\Rules\SvgSizeRule;
use App\Services\DivisionsService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DivisionsController
 * @package App\Http\Controllers\Masters
 */
class DivisionsController extends Controller
{
    /** @var string */
    private $role_name = 'divisions';

    /** @var */
    private $settings;

    /** @var DivisionsService  */
    private DivisionsService $service;

    /**
     * DivisionsController constructor.
     * @param DivisionsService $service
     */
    public function __construct(DivisionsService $service)
    {
        $this->settings = config('settings.views.divisions');
        $this->service = $service;
    }

    /**
     * 列表功能
     * @return View
     */
    public function index(): View
    {
        $divisions = $this->service->all();

        $custom = [
            'title' => '請上傳相關檔案',
            'type' => 'custom',
            'required' => true,
            'custom' => 'masters.divisions.custom',
            'name' => 'custom',
        ];

        $this->settings['edit_field'][] = $custom;

        return view('masters.divisions.index', [
            'role_name' => $this->role_name,
            'editField' => $this->settings['edit_field'],
            'field' => ['編號', '科別', 'Icon', '建立時間', '更新時間', '狀態', '編輯', '排序'],
            'datas' => $divisions,
        ]);
    }

    /**
     * 新增資料
     *
     * @param DivisionsStore $request
     * @return Response
     * @throws ValidationException
     */
    public function store(DivisionsStore $request): Response
    {
        $this->validate($request, ['icon' => [new SvgSizeRule($request->file('icon')->path(), 52, 52)]]);
        $this->validate($request, ['icon_hover' => [new SvgSizeRule($request->file('icon_hover')->path(), 52, 52)]]);

        $errors = $this->service->validateUniqueEnName($request->all(), null);

        if (!empty($errors)) {
            return response()->json(['errMsg' => $errors['status']], 400);
        }

        $uploadArray = ['icon', 'icon_hover', 'icon_android', 'icon_android_hover', 'icon_ios', 'icon_ios_hover'];

        foreach ($uploadArray as $key) {
            if (empty($request->file($key))) {
                continue;
            }

            $filePath = config('constants.s3.path') . date('Y') . '/' . date('m') . '/';
            $fileExtension = $request->file($key)->getClientOriginalExtension();
            $fileName = date('YmdHis') . '-' . Str::random(8) . '.' . $fileExtension;
            $s3Path = $filePath . $fileName;
            Storage::disk('s3')->put($s3Path, file_get_contents($request->file($key)->path()));
            $request->request->add([$key . '_full_url' => config('constants.cdn.url') . $s3Path]);
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
     * 帶出要編輯的資料
     *
     * @param DivisionsModel $division
     * @return Response
     */
    public function edit(DivisionsModel $division)
    {
        return response()->json($division);
    }

    /**
     * 更新資料
     *
     * @param DivisionsUpdate $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function update(DivisionsUpdate $request, $id)
    {
        $errors = $this->service->validate($id, $request->all());

        if (!empty($errors)) {
            return response()->json(['errMsg' => $errors['status']], 400);
        }

        $uploadArray = [];

        // 如果有上傳的檔案，重新上傳，再將網址拿去更新資料庫內
        if ($request->hasFile('icon')) {
            $this->validate($request, ['icon' => [new SvgSizeRule($request->file('icon')->path(), 52, 52)]]);
            $uploadArray[] = 'icon';
        }
        if ($request->hasFile('icon_hover')) {
            $this->validate($request, ['icon_hover' => [new SvgSizeRule($request->file('icon_hover')->path(), 52, 52)]]);
            $uploadArray[] = 'icon_hover';
        }
        if ($request->hasFile('icon_android')) {
            $uploadArray[] = 'icon_android';
        }
        if ($request->hasFile('icon_android_hover')) {
            $uploadArray[] = 'icon_android_hover';
        }
        if ($request->hasFile('icon_ios')) {
            $uploadArray[] = 'icon_ios';
        }
        if ($request->hasFile('icon_ios_hover')) {
            $uploadArray[] = 'icon_ios_hover';
        }

        foreach ($uploadArray as $key) {
            $filePath = config('constants.s3.path') . date('Y') . '/' . date('m') . '/';
            $fileExtension = $request->file($key)->getClientOriginalExtension();
            $fileName = date('YmdHis') . '-' . Str::random(8) . '.' . $fileExtension;
            $s3Path = $filePath . $fileName;
            Storage::disk('s3')->put($s3Path, file_get_contents($request->file($key)->path()));
            $request->request->add([$key . '_full_url' => config('constants.cdn.url') . $s3Path]);
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
     * 儲存排序
     *
     * @param DivisionsSaveSort $request
     * @return Response
     */
    public function saveSort(DivisionsSaveSort $request): Response
    {
        $data = $request->validated();

        $this->service->saveSort($data['masters_divisions']);

        return response()->success([]);
    }
}
