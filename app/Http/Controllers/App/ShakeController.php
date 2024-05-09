<?php

namespace App\Http\Controllers\App;

use App\Exports\ShakeExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShakeStore;
use App\Http\Requests\ShakeUpdate;
use App\Models\App\ShakeModel;
use App\Services\ShakeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ShakeController
 * @package App\Http\Controllers\App
 */
class ShakeController extends Controller
{
    /** @var string */
    private $roleName = 'shake';

    /** @var string */
    private $siteName = 'shake';

    /** @var ShakeService */
    private ShakeService $service;

    public function __construct(ShakeService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data = request()->all();

        $field = ['標題', '文案內容', '活動類型', '活動開始時間', '活動結束時間', '參與次數', '狀態'];

        $collections = $this->service->find($data);

        return view('app.shake.index', [
            'has_checkbox' => true,
            'role_name' => $this->roleName,
            'field' => $field,
            'datas' => $collections,
            'filters' => [],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('app.shake.create', [
            'role_name' => $this->roleName,
            'site_name' => $this->siteName,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ShakeStore $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function store(ShakeStore $request): RedirectResponse
    {
        $data = $request->validated();

        $this->service->validateTimes($data['shake_time_start'], $data['shake_time_end']);

        $this->service->store($data, \Auth::user());

        return redirect()->route('shake.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return View
     * @throws \Exception
     */
    public function edit($id): View
    {
        $model = ShakeModel::find($id);

        if ($model === null) {
            throw new \Exception('model notfound.');
        }

        return view('app.shake.edit', [
            'role_name' => $this->roleName,
            'site_name' => $this->siteName,
            'shake' => $model,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $id
     * @param ShakeUpdate $request
     * @return Response
     * @throws \Throwable
     */
    public function update(string $id, ShakeUpdate $request): Response
    {
        $model = ShakeModel::find($id);

        if ($model === null) {
            throw new \Exception('model notfound.');
        }

        $data = $request->validated();

        $this->service->validateTimes($data['shake_time_start'], $data['shake_time_end'], $id);

        $this->service->update($model, $data, \Auth::user());

        return redirect()->route('shake.index');
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     * @throws \Exception
     */
    public function download(Request $request): BinaryFileResponse
    {
        $ids = $request->input('shake_ids');

        $ids = explode(',', $ids);

        if (empty($ids)) {
            throw new \Exception('請勾選任一APP搖一搖');
        }

        $models = ShakeModel::with(['article', 'members'])->whereIn('shake_id', $ids)->orderByDesc('shake_id')->get();

        $filename = 'export_shake_member_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new ShakeExport($models), $filename)->deleteFileAfterSend(true);
    }
}
