<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Home\ToolsApi;
use App\Models\HomeArea\MeasureModel;

class ToolsApiController extends Controller
{
    /**
     * @return Response
     */
    public function tools(): Response
    {
        $tools = MeasureModel::active()
                ->orderBy('sort', 'desc')
                ->orderBy('id', 'desc')
                ->limit(4)
                ->get();

        $meta['more_url'] = null;
        return response()->success(ToolsApi::collection($tools), $meta);
    }
}
