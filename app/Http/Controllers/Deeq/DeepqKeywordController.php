<?php

namespace App\Http\Controllers\Deeq;

use App\Events\DeepqKeywordGenerating;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeepqKeywordGenerate;
use App\Http\Requests\DeepqKeywordStore;
use App\Http\Requests\DeepqKeywordUpdate;
use App\Models\Deepq\KeywordModel;
use App\Services\DeepqKeywordService;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

class DeepqKeywordController extends Controller
{
    /** @var string */
    private $role_name = 'deepq_keywords';

    /** @var */
    private $settings;

    /** @var DeepqKeywordService */
    private DeepqKeywordService $service;

    /**
     * DeepqKeywordController constructor.
     * @param DeepqKeywordService $service
     */
    public function __construct(DeepqKeywordService $service)
    {
        $this->settings = config('settings.views.deepq_keyword');
        $this->service = $service;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        $data = $this->service->lists();

        return view('deepq.keywords.index', [
            'role_name' => $this->role_name,
            'editField' => $this->settings['edit_field'],
            'field' => ['編號', '關鍵字', '開始時間', '結束時間', '刊登數量', '編輯'],
            'datas' => $data,
        ]);
    }

    /**
     * @param DeepqKeywordStore $request
     * @return Response
     */
    public function store(DeepqKeywordStore $request): Response
    {
        $data = $request->validated();

        $this->service->create($data, auth()->user());

        return response()->success([]);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function edit(int $id): Response
    {
        $model = KeywordModel::with(['questions' => function ($query) {
            $query->orderBy('sort');
        }])->findOrFail($id);

        return response()->json($model);
    }

    /**
     * @param DeepqKeywordUpdate $request
     * @param int $id
     * @return Response
     * @throws \Throwable
     */
    public function update(DeepqKeywordUpdate $request, int $id): Response
    {
        $data = $request->validated();

        $model = KeywordModel::findOrFail($id);

        $this->service->update($model, $data, auth()->user());

        return response()->json($request->input());
    }

    /**
     * @param int $id
     * @return Response
     * @throws \Throwable
     */
    public function destroy(int $id): Response
    {
        $model = KeywordModel::findOrFail($id);

        $this->service->delete($model, auth()->user());

        return response()->success([]);
    }

    /**
     * @param DeepqKeywordGenerate $request
     * @return Response
     */
    public function generate(DeepqKeywordGenerate $request): Response
    {
        $data = $request->validated();

        event(new DeepqKeywordGenerating(auth()->user(), $data));

        return response()->success([]);
    }
}
