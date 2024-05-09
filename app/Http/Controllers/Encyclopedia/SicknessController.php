<?php

namespace App\Http\Controllers\Encyclopedia;

use App\Http\Controllers\Controller;
use App\Http\Requests\SicknessIndex;
use App\Http\Requests\SicknessStore;
use App\Http\Requests\SicknessSaveSort;
use App\Services\SicknessService;
use App\Repositories\SicknessRepository;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;
use Request;

/**
 * Class SicknessController
 * @package App\Http\Controllers\Encyclopedia
 */
class SicknessController extends Controller
{
    /** @var string */
    private $role_name = 'sickness';

    /** @var string */
    private $site_name = 'sickness';

    /** @var */
    private $settings;

    /** @var SicknessService */
    private SicknessService $service;
    private SicknessRepository $repository;

    /**
     * SicknessController constructor.
     * @param SicknessService $service
     */
    public function __construct(SicknessService $service, SicknessRepository $repository)
    {
        $this->settings = config('settings.views.sickness');

        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @param SicknessIndex $request
     * @return View
     */
    public function index(SicknessIndex $request): View
    {

        $data = $request->validated();

        $body = $this->service->findByBodyAndOrgan($data);

        $custom = [
            'title' => '請選擇到器官組織',
            'type' => 'custom',
            'required' => true,
            'custom' => 'encyclopedia.sickness.custom',
            'name' => 'custom',

        ];
        $this->settings['edit_field'][] = $custom;


        $sickness = optional($body->organs)->pluck('sickness')->flatten()->unique('id')->sortBy('sort')->values();

        return view('encyclopedia.sickness.index', [
            'role_name' => $this->role_name,
            'editField' => $this->settings['edit_field'],
            'field' => ['編號', '疾病名稱', '建立時間', '更新時間', '狀態', '編輯', '排序'],
            'filters' => ['bodies' => $this->service->filterByBodies()],
            'datas' => $sickness,
        ]);
    }

    public function edit($id)
    {
        return response()->json($this->repository->find($id));
    }

    public function update(SicknessStore $request, $id)
    {
        
        $processedName = special_char_half_to_full($request->input('name'));

        $request->merge(['name' => $processedName]);

        $request->request->add([
            'status' => $request->input('status'),
            'updated_user' => auth()->user()->name,
            'create_organ_id_array' => array_unique(explode(',', $request->input('create_organ_id_string'))),
            'id' => $id,
        ]);


        $this->service->update($request->input());

        return response()->json($request->input(), 200);
    }

    /**
     * 新增疾病資料
     */
    public function store(SicknessStore $request) 
    {
        $processedName = special_char_half_to_full($request->input('name'));

        $request->merge(['name' => $processedName]);

        $request->request->add([
            'status' => $request->input('status'),
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name,
            'create_organ_id_array' => array_unique(explode(',', $request->input('create_organ_id_string'))),
        ]);

        


        $this->service->create($request->input());

        return response()->json($request->input(), 200);
    }

    /**
     * @param SicknessSaveSort $request
     * @return Response
     */
    public function saveSort(SicknessSaveSort $request): Response
    {
        $data = $request->validated();

        $this->service->saveSort($data['encyclopedia_sickness']);

        return response()->success([]);
    }
}
