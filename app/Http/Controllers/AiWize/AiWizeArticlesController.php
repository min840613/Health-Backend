<?php

namespace App\Http\Controllers\AiWize;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AiWizeService;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Aiwize\AiwizeModel;

class AiWizeArticlesController extends Controller
{
    /** @var string */
    private $role_name = 'aiwize_articles';

    /** @var */
    private $settings;

    /** @var AiWizeService */
    private AiWizeService $service;

    public function __construct(AiWizeService $service)
    {
        $this->settings = config('settings.views.aiwize');
        $this->service = $service;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        $data = $this->service->lists();

        if ($data) {
            foreach ($data as $key => $value) {
                $table_title = ($this->extractTitle($value['long_title'], "1.", "(")) ? $this->extractTitle($value['long_title'], "1.", "(") : 'no title detected';
                $data[$key]['table_title'] = $table_title;
            }
        }

        return view('aiwize.index', [
            'role_name' => $this->role_name,
            'editField' => $this->settings['edit_field'],
            'field' => ['編號', 'AiWize ID', 'AI Wize發佈時間', '文章ID', '標題', '選取者', '狀態', '動作'],
            'datas' => $data,
        ]);
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
        //
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
     * @param int $id
     * @return Response
     */
    public function edit(int $id): Response
    {
        $model = AiwizeModel::findOrFail($id);

        return response()->json($model);
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
        //
    }

    /**
     * @param int $id
     * @return Response
     * @throws \Throwable
     */
    public function destroy(int $id): Response
    {
        $model = AiwizeModel::findOrFail($id);

        $this->service->delete($model, auth()->user());

        return response()->success([]);
    }

    private function extractTitle($string, $start, $finish)
    {
        $string = " " . $string;
        $position = strpos($string, $start);
        if ($position == 0) {
            return "";
        }
        $position += strlen($start);
        $length = strpos($string, $finish, $position) - $position;
        return substr($string, $position, $length);
    }
}
