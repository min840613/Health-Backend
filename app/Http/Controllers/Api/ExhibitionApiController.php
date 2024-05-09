<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExhibitionApiTopic;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\ExhibitionApiResource;
use Illuminate\Support\Carbon;
use App\Models\HealthExhibitionListModel;


class ExhibitionApiController extends Controller
{
    /** @var int 熱搜取得筆數 */
    public const TOPIC_COUNT = 7;

    /**
     * @param ExhibitionApiTopic $request
     * @return Response
     */
    public function topic(ExhibitionApiTopic $request): Response
    {
        $data = $request->validated();

        $healthExhibitionListModel = new HealthExhibitionListModel();

        $exhibitions = $healthExhibitionListModel->where('start_at', '<=', Carbon::now())
            ->where('end_at', '>=', Carbon::now())
            ->orderBy('id', 'desc')
            ->limit($data['count'] ?? self::TOPIC_COUNT)
            ->get();

        return response()->success(ExhibitionApiResource::collection($exhibitions),[
            'name' => '專題',
            'main_category' => '專題',
            'main_category_en' => 'topic',
        ]);
    }
}
