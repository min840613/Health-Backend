<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeywordApiIndex;
use App\Models\KeywordClickCountModel;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Class KeywordApiController
 * @package App\Http\Controllers\Api
 */
class KeywordApiController extends Controller
{
    /** @var int 熱搜取得筆數 */
    public const KEYWORD_COUNT = 7;

    /**
     * @param KeywordApiIndex $request
     * @return Response
     */
    public function index(KeywordApiIndex $request): Response
    {
        $data = $request->validated();
        $keywords = [];
        $S3Data = $this->getAwsS3KeywordsFile();
        if($S3Data):
            // $keywords_arr = explode(',',$S3Data);
            $keywords_arr = json_decode($S3Data,true);
            if($keywords_arr && $keywords_arr['keywords']){
                foreach($keywords_arr['keywords'] as $k=>$v):
                    if($k < ($data['count'] ?? self::KEYWORD_COUNT)):
                        $keywords[] = $v;
                    endif;
                endforeach;
            }
        endif;
        if(!$keywords):
            $keywords = KeywordClickCountModel::where('date', '>=', now()->subDays(2)->startOfDay())
                ->whereNotIn('keyword', ['口交', '性交', '保險套', '潤滑劑', '性愛', '精液', '性行為', 'AV男優', '性功能', '性能力', '做愛', '勃起', '性慾', '性治療師'])
                ->orderBy('click_count')
                ->limit($data['count'] ?? self::KEYWORD_COUNT)
                ->get();
            return response()->success($keywords->pluck('keyword'));
        endif;
        return response()->success($keywords);
    }

    public function getAwsS3KeywordsFile()
    {
        $filePath = env('AWS_S3_DEEP_FOLDER','health2.0-pre').'/keyword/keywords_list.txt';
        $fileContent = Storage::disk('s3')->get($filePath);
        return $fileContent;
    }
}
