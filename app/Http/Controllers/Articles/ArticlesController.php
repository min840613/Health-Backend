<?php

namespace App\Http\Controllers\Articles;

use App\Events\ArticleStored;
use App\Events\ArticleUpdated;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ArticlesAppendCategories;
use App\Http\Requests\ArticlesIndex;
use App\Models\Articles\ArticleModel;
use App\Services\ArticlesService;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Categories\MainCategoriesModel;
use App\Models\Authors\AuthorsModel;
use App\Models\Articles\EndTextModel;
use App\Models\Encyclopedia\BodyModel;
use App\Models\Encyclopedia\OrgansModel;
use App\Models\Encyclopedia\SicknessModel;
use App\Models\Encyclopedia\SicknessToOrganModel;
use App\Models\Articles\ArticleCategoriesModel;
use App\Models\Articles\ArticleCategoriesMappingsModel;
use App\Models\Articles\ArticlesSicknessModel;
use App\Models\Articles\ArticlesFurtherReadingModel;
use App\Models\Articles\ArticleTagMappingModel;
use App\Models\Masters\MastersModel;
use App\Models\ThirdPartyFeed\LineArticleConditionModel;
use App\Models\ThirdPartyFeed\LineArticlesModel;
use App\Models\ThirdPartyFeed\MixerboxArticleConditionModel;
use App\Models\ThirdPartyFeed\MixerboxArticlesModel;
use App\Models\ThirdPartyFeed\YahooRssModel;
use App\Models\Aiwize\AiwizeModel;
use App\Rules\HasSicknessIdRule;
use App\Rules\YahooExtDataRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Enums\MainCategoriesType;

/**
 * Class ArticlesController
 * @package App\Http\Controllers\Articles
 */
class ArticlesController extends Controller
{
    /** @var string */
    private $role_name = 'articles';

    private $site_name = 'articles';

    /** @var ArticlesService */
    protected ArticlesService $service;

    private $MainCategoriesModel;

    private $AuthorsModel;

    private $EndTextModel;

    private $BodyModel;

    private $OrgansModel;

    private $SicknessModel;

    private $SicknessToOrganModel;

    private $ArticleCategoriesModel;

    private $ArticleModel;

    private $ArticleCategoriesMappingsModel;

    private $ArticlesSicknessModel;

    private $ArticlesFurtherReadingModel;

    private $ArticleTagMappingModel;

    private $MastersModel;

    private $LineArticleConditionModel;

    private $EncyclopediaData;

    private $GuangbianData;

    private $LineArticleCondition;

    private $LineArticlesModel;

    private $MixerboxArticleConditionModel;

    private $MixerboxArticlesModel;

    private $MixerboxArticleCondition;

    private $YahooRssModel;

    /**
     * ArticlesController constructor.
     * @param ArticlesService $service
     */
    public function __construct(
        ArticlesService $service,
        MainCategoriesModel $MainCategoriesModel,
        AuthorsModel $AuthorsModel,
        EndTextModel $EndTextModel,
        BodyModel $BodyModel,
        OrgansModel $OrgansModel,
        SicknessModel $SicknessModel,
        SicknessToOrganModel $SicknessToOrganModel,
        ArticleCategoriesModel $ArticleCategoriesModel,
        ArticleModel $ArticleModel,
        ArticleCategoriesMappingsModel $ArticleCategoriesMappingsModel,
        ArticlesSicknessModel $ArticlesSicknessModel,
        ArticlesFurtherReadingModel $ArticlesFurtherReadingModel,
        ArticleTagMappingModel $ArticleTagMappingModel,
        MastersModel $MastersModel,
        LineArticleConditionModel $LineArticleConditionModel,
        LineArticlesModel $LineArticlesModel,
        MixerboxArticleConditionModel $MixerboxArticleConditionModel,
        MixerboxArticlesModel $MixerboxArticlesModel,
        YahooRssModel $YahooRssModel
    ) {
        $this->service = $service;
        $this->MainCategoriesModel = $MainCategoriesModel;
        $this->AuthorsModel = $AuthorsModel;
        $this->EndTextModel = $EndTextModel;
        $this->BodyModel = $BodyModel;
        $this->OrgansModel = $OrgansModel;
        $this->SicknessModel = $SicknessModel;
        $this->SicknessToOrganModel = $SicknessToOrganModel;
        $this->ArticleCategoriesModel = $ArticleCategoriesModel;
        $this->ArticleModel = $ArticleModel;
        $this->ArticleCategoriesMappingsModel = $ArticleCategoriesMappingsModel;
        $this->ArticlesSicknessModel = $ArticlesSicknessModel;
        $this->ArticlesFurtherReadingModel = $ArticlesFurtherReadingModel;
        $this->ArticleTagMappingModel = $ArticleTagMappingModel;
        $this->MastersModel = $MastersModel;
        $this->LineArticleConditionModel = $LineArticleConditionModel;
        $this->LineArticlesModel = $LineArticlesModel;
        $this->MixerboxArticleConditionModel = $MixerboxArticleConditionModel;
        $this->MixerboxArticlesModel = $MixerboxArticlesModel;
        $this->YahooRssModel = $YahooRssModel;

        $this->EncyclopediaData = $this->MainCategoriesModel::where('en_name', 'encyclopedia')
                                                    ->first();
        $this->GuangbianData = $this->MainCategoriesModel::where('en_name', 'Guangbian')
                                                    ->first();

        $this->LineArticleCondition = $this->LineArticleConditionModel::get()->pluck('category_name', 'category_id')->toArray();
        $this->MixerboxArticleCondition = $this->MixerboxArticleConditionModel::get()->pluck('category_name', 'category_id')->toArray();
    }

    public function index(ArticlesIndex $request): View
    {
        // 獲取查詢字符串的值
        $articleCategoryType = $request->query('type');

        // 獲取經過驗證的數據並將類別類型添加到其中
        $data = array_merge($request->validated(), ['type' => $articleCategoryType]);

        // 根據搜索參數檢索文章集合
        $collections = $this->service->find($data);

        // 獲取過濾器選項
        $filters = $this->service->filters();

        // 定義要在視圖中顯示的字段
        $field = ['標題', '主圖', '主分類', '子分類', '上稿者', '發佈時間', '最後更新時間', '狀態'];

        // 根據分類類型決定要顯示的視圖
        $view = ($articleCategoryType == 'ad') ? 'articles.ad.index' : 'articles.index';

        // 返回相應的視圖和所需數據
        return view($view, [
            'has_checkbox' => true,
            'role_name' => ($articleCategoryType == 'ad') ? 'articles_ad' : 'articles',
            'field' => $field,
            'datas' => $collections,
            'filters' => $filters
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(request $request): View
    {
        $ArticlesCategoryType = $request->input('type');

        $role_name = $this->role_name;
        $site_name = $this->site_name;

        // 主分類
        $CategoriesData = $this->MainCategoriesModel::where('categories_status', '1')
                                                        ->whereNot('categories_type', '3')
                                                        ->OrderBy('sort_index', 'desc')
                                                        ->select('categories_id', 'name')
                                                        ->get();
        foreach ($CategoriesData as $v) :
            $CategoriesDataList[$v['categories_id']] = $v['name'];
        endforeach;

        // 上稿者管理
        $AuthorsData = $this->AuthorsModel::where('status', true)
                                            ->get();
        foreach ($AuthorsData as $v) :
            $AuthorsDataList[$v['id']] = $v['name'];
        endforeach;

        // 文末廣宣
        $ArticleEndData = $this->EndTextModel::where(['status' => 1,'text_type' => 1])
                                            ->OrderBy('order_num', 'asc')
                                            ->get();

        // 取得醫學百科身體部位
        $HealthBody = $this->BodyModel::OrderBy('sort', 'asc')->get();
        foreach ($HealthBody as $v) :
            $HealthBodyList[$v['id']] = $v['name'];
        endforeach;

        // 取得醫學百科分類
        $HealthArticlesCategories = $this->ArticleCategoriesModel::where('status', 1)
                                                                    ->OrderBy('sort', 'asc')
                                                                    ->OrderBy('id', 'desc')
                                                                    ->get();
        $HealthArticlesCategoriesList = [
            0   => '請選擇'
        ];
        foreach ($HealthArticlesCategories as $v) :
            $HealthArticlesCategoriesList[$v['id']] = $v['name'];
        endforeach;

        foreach ($ArticleEndData as $v) :
            $ArticleEndDataList[$v['content']] = $v['short_title'];
        endforeach;

        $field = [
            'categories' => [
                'type'          => 'category_selectize',
                'title'         => '分類',
                'placeholder'   => '分類',
                'required'      => true,
                'comment'       => null,
                'options'       => $CategoriesDataList
            ],
            'medicine_article_sickness' => [
                'type'          => 'medicine_selectize',
                'title'         => '醫學百科疾病',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
            ],
            'medicine_article_category' => [
                'type'          => 'medicine_category_select',
                'title'         => '醫學百科分類',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => $HealthArticlesCategoriesList
            ],
            'publish'       => [
                'type'          => 'datetime',
                'title'         => '發佈時間',
                'placeholder'   => '發佈時間',
                'required'      => true,
                'comment'       => null,
            ],
            'title'         => [
                'type'          => 'text',
                'title'         => '文章標題',
                'placeholder'   => '文章標題',
                'required'      => true,
                'comment'       => null,
            ],
            'og_title'      => [
                'type'          => 'text',
                'title'         => '社群標題',
                'placeholder'   => '社群標題',
                'required'      => true,
                'comment'       => null,
            ],
            'seo_title'     => [
                'type'          => 'text',
                'title'         => 'SEO標題',
                'placeholder'   => 'SEO標題',
                'required'      => true,
                'comment'       => null,
            ],
            'author'        => [
                'type'          => 'selectize_author',
                'title'         => '上稿者名稱',
                'placeholder'   => '上稿者名稱',
                'required'      => true,
                'comment'       => null,
            ],
            'adult_flag'    => [
                'type'          => 'radio',
                'title'         => '18禁文章',
                'placeholder'   => '18禁文章',
                'required'      => false,
                'comment'       => null,
            ],
            'collaborator'  => [
                'type'=> 'select_collaborator',
                'title'=> '協作者',
                'placeholder'=> '協作者',
                'required'=> false,
                'comment'=> null
            ],
            'image'         => [
                'type'          => 'image',
                'title'         => '主圖 URL',
                'placeholder'   => '主圖 URL',
                'required'      => true,
                'comment'       => '建議圖片尺寸：1024 * 576 px',
            ],
            'ogimage'       => [
                'type'          => 'image',
                'title'         => '社群分享圖URL',
                'placeholder'   => '社群分享圖URL',
                'required'      => true,
                'comment'       => null,
            ],
            'video_id'      => [
                'type'          => 'text',
                'title'         => '主影音',
                'placeholder'   => '輸入youtube 影音ID',
                'required'      => false,
                'comment'       => null,
            ],
            'article_content'   => [
                'type'          => 'textarea',
                'title'         => '內容',
                'placeholder'   => '內容',
                'required'      => true,
                'comment'       => null,
            ],
            'extended_reading'   => [
                'type'          => 'extended_text',
                'title'         => '插入看更多',
                'placeholder'   => '請輸入文章ID',
                'required'      => false,
                'comment'       => null,
            ],
            'further_reading'   => [
                'type'          => 'further_text',
                'title'         => '延伸閱讀',
                'placeholder'   => '延伸閱讀',
                'required'      => false,
                'comment'       => null,
            ],
            'tag'           => [
                'type'          => 'selectize',
                'title'         => 'TAG',
                'placeholder'   => '限制 8 個 TAG',
                'required'      => false,
                'comment'       => null,
            ],
            'match_searchs' => [
                'type'          => 'selectize_text',
                'title'         => '字串搜尋',
                'placeholder'   => '單筆字數限制為30個字',
                'required'      => false,
                'comment'       => null,
            ],
            'end_of_text' => [
                'type'          => 'end_select',
                'title'         => '文末廣宣',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => $ArticleEndDataList
            ],
            'talent_category_id' => [
                'type'          => 'master_select',
                'title'         => '醫師專家達人',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => []
            ],
            'articles_status'   => [
                'type'          => 'select',
                'title'         => '狀態',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => [0 => '下架', 1 => '發佈']
            ],
            'is_line_article'   => [
                'type'          => 'line_article_select',
                'title'         => 'LINE 文章供稿',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => [0 => '否', 1 => '是']
            ],
            'is_line_rss'   => [
                'type'          => 'line_select',
                'title'         => 'LINE 影音供稿',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => [0 => '否', 1 => '是']
            ],
            'is_mixerbox_article'   => [
                'type'          => 'mixerbox_article_select',
                'title'         => 'MixerBox 供稿',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => [0 => '否', 1 => '是']
            ],
            'is_yahoo_rss'   => [
                'type'          => 'yahoo_select',
                'title'         => 'YAHOO供稿',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => [0 => '否', 1 => '是']
            ],
            'yahoo_ext'   => [
                'type'          => 'yahoo_ext_select',
                'title'         => 'YAHOO供稿延伸閱讀',
                'placeholder'   => '',
                'required'      => true,
                'comment'       => null,
                'options'       => [0 => '請選擇']
            ]
        ];

        // 醫學百科ID
        $Encyclopedia = $this->EncyclopediaData;

        $LineArticleCondition = $this->LineArticleCondition;
        $MixerboxArticleCondition = $this->MixerboxArticleCondition;

        //AiWize
        $aiWize = [];
        $aiWizeID = request()->query('aiwize');
        if (!empty($aiWizeID)) {
            $aiWize['ai_wize_id'] = $aiWizeID;
            $aiWize['ai_long_title'] = request()->query('lt');
            $aiWize['ai_short_title'] = request()->query('st');
        }


        if ($ArticlesCategoryType == 'ad') :
            unset($field['is_line_article'], $field['is_line_rss'], $field['is_mixerbox_article'], $field['is_yahoo_rss']);
            return view(
                'articles.ad.create',
                compact('role_name', 'site_name', 'field', 'CategoriesDataList', 'AuthorsDataList', 'HealthBodyList', 'ArticlesCategoryType', 'Encyclopedia', 'LineArticleCondition', 'MixerboxArticleCondition')
            );
        else :
            return view(
                'articles.create',
                compact('role_name', 'site_name', 'field', 'CategoriesDataList', 'AuthorsDataList', 'HealthBodyList', 'ArticlesCategoryType', 'Encyclopedia', 'LineArticleCondition', 'MixerboxArticleCondition', 'aiWize')
            );
        endif;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $LineArticleCondition = $this->LineArticleCondition;
        $MixerboxArticleCondition = $this->MixerboxArticleCondition;

        $ArticlesCategoryType = $request->input('type');

        $validateRules = [
            'categories'    => ['array', new HasSicknessIdRule()],
            'categories.*'  => 'required|integer',
            'publish'       => 'required',
            'title'         => 'required',
            'og_title'      => 'required',
            'seo_title'     => 'required',
            'author'        => 'required',
            'author_type'   => 'required',
            'image'         => 'required',
            'ogimage'       => 'required',
            'article_content'   => 'required',
            'yahoo_ext_data'    => [new YahooExtDataRule($request->input('is_yahoo_rss'))],
            'video_id' => 'nullable|regex:/^[A-Za-z0-9_-]+$/',
            'is_line_article' => ['required_if:type,web',
                                    function ($attribute, $value, $fail) use ($request, $LineArticleCondition) {
                                        if ($value == 1) {
                                            if (!array_key_exists($request->input('categories')[0], $LineArticleCondition)) {
                                                $fail('當:attribute『是』時，只允許主分類為：' . implode("、", $LineArticleCondition));
                                            }
                                            if ($request->input('adult_flag') == 1) {
                                                $fail('當:attribute『是』時，不能是18禁文章');
                                            }
                                            if ($request->input('articles_status') == 0) {
                                                $fail('當:attribute『是』時，不能是下架狀態');
                                            }
                                        }
                                    },],
            'is_line_rss' => ['required_if:type,web',
                                function ($attribute, $value, $fail) use ($request) {
                                    if ($request->input('type') != 'ad' && $value == 1) {
                                        if ($request->input('adult_flag') == 1) {
                                            $fail('當:attribute『是』時，不能是18禁文章');
                                        }
                                        if ($request->input('articles_status') == 0) {
                                            $fail('當:attribute『是』時，不能是下架狀態');
                                        }
                                    }
                                },],
            'video_file_name' => [function ($attribute, $value, $fail) use ($request) {
                if (
                    $request->input('type') != 'ad'
                                        && $request->input('is_line_rss') == 1
                                        && empty($value)
                ) {
                    $fail('當Line影音供稿『是』時，Line供稿影片為必填');
                }
            },],
            'is_mixerbox_article' => ['required_if:type,web',
                                        function ($attribute, $value, $fail) use ($request, $MixerboxArticleCondition) {
                                            if ($value == 1) {
                                                if (!array_key_exists($request->input('categories')[0], $MixerboxArticleCondition)) {
                                                    $fail('當:attribute『是』時，只允許主分類為：' . implode("、", $MixerboxArticleCondition));
                                                }
                                                if ($request->input('adult_flag') == 1) {
                                                    $fail('當:attribute『是』時，不能是18禁文章');
                                                }
                                                if ($request->input('articles_status') == 0) {
                                                    $fail('當:attribute『是』時，不能是下架狀態');
                                                }
                                            }
                                        },],
            'is_yahoo_rss' => ['required_if:type,web',
                                function ($attribute, $value, $fail) use ($request) {
                                    if ($request->input('type') != 'ad' && $value == 1) {
                                        if ($request->input('adult_flag') == 1) {
                                            $fail('當:attribute『是』時，不能是18禁文章');
                                        }
                                        if ($request->input('articles_status') == 0) {
                                            $fail('當:attribute『是』時，不能是下架狀態');
                                        }
                                    }
                                },],
            'talent_category_id' => ['required_if:master_type,1,2,3', function ($attribute, $value, $fail) use ($request) {
                if ($value < 1 && $request->master_type > 0) {
                    $fail('請選擇醫師姓名');
                }
            },],
        ];

        $validateAttributes = [
            'categories.*'  => '分類',
            'publish'       => '發佈時間',
            'title'         => '文章標題',
            'medicine_article_sickness_id'  => '醫學百科疾病',
            'medicine_article_category'     => '醫學百科分類',
            'og_title'      => '社群標題',
            'seo_title'     => 'SEO標題',
            'author'        => '上稿者名稱',
            'author_type'   => '上稿者分類',
            'adult_flag' => '18禁文章',
            'image'         => '主圖URL',
            'ogimage'       => '社群分享圖URL',
            'article_content'   => '內容',
            'articles_status' => '狀態',
            'is_line_article'       => 'Line文章供稿',
            'is_line_rss'       => 'Line影音供稿',
            'video_file_name'   => 'Line供稿影片',
            'is_mixerbox_article' => 'MixerBox供稿',
            'is_yahoo_rss' => 'YAHOO供稿',
            'master_type' => '醫師專家達人',
            'talent_category_id' => '專家',
        ];

        $validateMessages = [
            'required_if' => '當『:other』欄位為『是』時，『:attribute』為必填',
            'video_id.regex' => '主影音欄位，請輸入正確的Youtube參數(可輸入規則：大小寫英文、數字、底線、破折號)',
            'talent_category_id.required_if' => '請選擇醫師專家達人姓名',
        ];

        $this->validate($request, $validateRules, $validateMessages, $validateAttributes);

        $PostData = $request->all();

        try {
            $article = DB::transaction(function () use ($PostData, $ArticlesCategoryType) {
                // 新增文章，產生Article_ID
                $HealthArticleData = [
                    'articles_status'       => $PostData['articles_status'],
                    'publish'               => $PostData['publish'],
                    'title'                 => $PostData['title'],
                    'og_title'              => $PostData['og_title'],
                    'seo_title'             => $PostData['seo_title'],
                    'author'                => $PostData['author'],
                    'author_type'           => $PostData['author_type'],
                    'medicine_article_category_id'  => $PostData['medicine_article_category'],
                    'adult_flag'            => $PostData['adult_flag'],
                    'image'                 => $PostData['image'],
                    'image_alt'             => ($PostData['image_alt'] ? $PostData['image_alt'] : ''),
                    'ogimage'               => $PostData['ogimage'],
                    'video_id'              => ($PostData['video_id'] ? $PostData['video_id'] : ''),
                    'fb_ia_video'           => '',
                    'tag'                   => ($PostData['tag'] ? $PostData['tag'] : ''),
                    'match_searchs'         => ($PostData['match_searchs'] ? $PostData['match_searchs'] : ''),
                    'extended_article'      => ($PostData['further_reading'] ? $PostData['further_reading'] : ''),
                    'article_content'       => $PostData['article_content'],
                    'is_line_article'       => isset($PostData['is_line_article']) && $PostData['is_line_article'] ? $PostData['is_line_article'] : 0,
                    'is_line_rss'           => isset($PostData['is_line_rss']) && $PostData['is_line_rss'] ? $PostData['is_line_rss'] : 0,
                    'video_file_name'       => isset($PostData['video_file_name']) && $PostData['video_file_name'] ? $PostData['video_file_name'] : '',
                    'is_mixerbox_article'       => isset($PostData['is_mixerbox_article']) && $PostData['is_mixerbox_article'] ? $PostData['is_mixerbox_article'] : 0,
                    'is_yahoo_rss'          => isset($PostData['is_yahoo_rss']) && $PostData['is_yahoo_rss'] ? $PostData['is_yahoo_rss'] : 0,
                    'collaborator'          => $PostData['collaborator'] ?? null,
                    'created_user'          => auth()->user()->name,
                    'updated_user'          => auth()->user()->name,
                    'match_url'             => ''
                ];

                $ArticlesInsertData = $this->ArticleModel::create($HealthArticleData);
                $ArticleId = $ArticlesInsertData['articles_id'];

                if (isset($PostData['aiWizeID'])) {
                    // update AiWize table
                    $AiWizeData = [
                        'health_article_id' => $ArticleId,
                        'status' => 0,
                        'choose_user' => auth()->user()->name
                    ];
                    AiwizeModel::find($PostData['aiWizeID'])->update($AiWizeData);
                }


                // 醫師專家
                $ArticlesInsertData->masters()->detach();
                $PostData['talent_category_id'] = array_filter($PostData['talent_category_id']);
                if (!empty($PostData['talent_category_id'])) {
                    $ArticlesInsertData->masters()->sync($PostData['talent_category_id']);
                }

                // 新增文章對應分類
                $this->ArticleCategoriesMappingsModel::where('article_id', $ArticleId)->delete();
                if ($ArticlesCategoryType == 'ad') :
                    $CategoriesData = [
                        0   => $this->GuangbianData['categories_id']
                    ];
                    $SubCategoriesData = [
                        0   => ['']
                    ];
                else :
                    $CategoriesData = [];
                    $SubCategoriesData = [];
                endif;

                foreach ($PostData['categories'] as $k => $v) :
                    if (!in_array($v, $CategoriesData)) :
                        $CategoriesData[] = $v;
                    endif;
                    if (isset($PostData['subcategories'][$k]) && $PostData['subcategories'][$k]) :
                        $SubCategoriesData[$v][] = $PostData['subcategories'][$k];
                    endif;
                endforeach;
                foreach ($CategoriesData as $k => $v) :
                    if ($v) :
                        $ArticleMappingCategoriesData = [
                            'article_id'    => $ArticleId,
                            'category_id'   => $v,
                            'sort'          => $k
                        ];
                        $this->ArticleCategoriesMappingsModel::create($ArticleMappingCategoriesData);
                    endif;
                endforeach;
                $i = 0;
                foreach ($SubCategoriesData as $k => $v) :
                    foreach ($v as $vv) :
                        if ($vv) :
                            $ArticleMappingCategoriesData = [
                                'article_id'    => $ArticleId,
                                'category_id'   => $vv,
                                'sort'          => $i,
                                'parent'        => $k
                            ];
                            $this->ArticleCategoriesMappingsModel::create($ArticleMappingCategoriesData);
                            $i++;
                        endif;
                    endforeach;
                endforeach;

                // 新增文章對應疾病
                $this->ArticlesSicknessModel::where('article_id', $ArticleId)->delete();

                $SicknessArrData = explode(',', $PostData['medicine_article_sickness_id']);
                foreach ($SicknessArrData as $k => $v) :
                    if ($v) :
                        $SicknessData = [
                            'article_id'            => $ArticleId,
                            'health_sickness_id'    => $v,
                            'created_user'          => auth()->user()->name,
                            'updated_user'          => auth()->user()->name
                        ];
                        $this->ArticlesSicknessModel::create($SicknessData);
                    endif;
                endforeach;

                // 延伸閱讀
                $this->ArticlesFurtherReadingModel::where('article_id', $ArticleId)->where('type', 'article')->delete();

                $FurtherReadingArrData = explode(',', $PostData['further_reading']);
                foreach ($FurtherReadingArrData as $v) :
                    if ($v) :
                        $FurtherReadingData = [
                            'article_id'                => $ArticleId,
                            'recommendation_article_id' => $v,
                            'type'                      => 'article',
                            'created_user'              => auth()->user()->name,
                            'updated_user'              => auth()->user()->name
                        ];
                        $this->ArticlesFurtherReadingModel::create($FurtherReadingData);
                    endif;
                endforeach;

                // Tag
                $this->ArticleTagMappingModel::where('article_id', $ArticleId)->delete();

                $TagArrData = explode(',', $PostData['tag']);
                foreach ($TagArrData as $v) :
                    if ($v) :
                        $TagData = [
                            'article_id'    => $ArticleId,
                            'tag'           => trim($v),
                            'created_user'  => auth()->user()->name,
                            'updated_user'  => auth()->user()->name
                        ];
                        $this->ArticleTagMappingModel::create($TagData);
                    endif;
                endforeach;

                //Line 文章供稿
                if ($HealthArticleData['is_line_article'] == 1) {
                    $this->LineArticlesModel::create([
                        'article_id' => $ArticleId,
                        'created_user'  => auth()->user()->name,
                        'updated_user'  => auth()->user()->name
                    ]);
                }

                //MixerBox 供稿
                if ($HealthArticleData['is_mixerbox_article'] == 1) {
                    $this->MixerboxArticlesModel::create([
                        'article_id' => $ArticleId,
                        'created_user'  => auth()->user()->name,
                        'updated_user'  => auth()->user()->name
                    ]);
                }

                if ($HealthArticleData['is_yahoo_rss'] == 1) {
                    // Yahoo供稿 延伸閱讀
                    // $this->ArticlesFurtherReadingModel::where('article_id',$ArticleId)->where('type','yahoo')->delete();
                    $YahooExtArrData = explode(',', $PostData['yahoo_ext_data']);
                    foreach ($YahooExtArrData as $v) :
                        if ($v) :
                            $YahooExtData = [
                                'article_id'                => $ArticleId,
                                'recommendation_article_id' => $v,
                                'type'                      => 'yahoo',
                                'created_user'              => auth()->user()->name,
                                'updated_user'              => auth()->user()->name
                            ];
                            $this->ArticlesFurtherReadingModel::create($YahooExtData);
                        endif;
                    endforeach;

                    $this->YahooRssModel::create(
                        [
                            'article_id' => $ArticleId,
                            'created_user' => auth()->user()->name,
                            'updated_user' => auth()->user()->name
                        ]
                    );
                }

                return $ArticlesInsertData;
            });

            // todo: 同步資料
            event(new ArticleStored($article));

            if ($ArticlesCategoryType == 'ad') :
                return redirect()->route('articles_ad.index')
                                    ->with('success', '儲存成功');
            else :
                return redirect()->route('articles.index')
                                    ->with('success', '儲存成功');
            endif;
        } catch (\Throwable $th) {
            $err = $th->getMessage();
            Log::alert($th);

            if ($ArticlesCategoryType == 'ad') :
                return redirect()->route('articles_ad.create')
                                ->withErrors($err)
                                ->withInput();
            else :
                return redirect()->route('articles.create')
                                ->withErrors($err)
                                ->withInput();
            endif;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function show(Request $request, $id): View
    {
        $ArticlesCategoryType = $request->input('type');

        $article = ArticleModel::find($id);

        if ($article === null) {
            abort(404);
        }

        $article->load(['recommendations', 'yahooRecommendations', 'mainCategories', 'subCategories', 'tags']);

        if ($ArticlesCategoryType == 'ad') :
            return view('articles.ad.show', [
                'role_name' => $this->role_name,
                'article' => $article,
            ]);
        else :
            return view('articles.show', [
                'role_name' => $this->role_name,
                'article' => $article,
            ]);
        endif;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, $id): View
    {
        $LineArticleCondition = $this->LineArticleCondition;
        $MixerboxArticleCondition = $this->MixerboxArticleCondition;

        $ArticlesCategoryType = $request->query('type');

        $role_name = $this->role_name;
        $site_name = $this->site_name;

        $OldMainCategoriesData = [];
        if (old()) :
            $OldMainCategoriesData = $this->MainCategoriesModel::whereIn('categories_id', old('categories'))->get();
        endif;

        // 取得資料庫資料
        $ArticleData = $this->ArticleModel::with('mainCategories')
                                        ->with('ArticlesSicknessMapping')
                                        ->with('recommendations')
                                        ->with('yahooRecommendations')
                                        ->with('masters')
                                        ->findOrFail($id);
        if ($OldMainCategoriesData) :
            $ArticleData['mainCategories'] = $OldMainCategoriesData;
            foreach ($OldMainCategoriesData as $k => $v) :
                $SubCategoriesMappingData = $this->ArticleCategoriesMappingsModel::where('article_id', $id)
                                                        ->where('parent', $v['categories_id'])
                                                        ->get();
                $subCategoriesID = [];
                if ($SubCategoriesMappingData) :
                    foreach ($SubCategoriesMappingData as $kk => $vv) :
                        $subCategoriesID[] = $vv['category_id'];
                    endforeach;
                endif;
                $ArticleData['mainCategories'][$k]->subCategoriesID = $subCategoriesID;
            endforeach;
        else :
            foreach ($ArticleData['mainCategories'] as $k => $v) :
                $SubCategoriesMappingData = $this->ArticleCategoriesMappingsModel::where('article_id', $id)
                                                        ->where('parent', $v['categories_id'])
                                                        ->get();
                $subCategoriesID = [];
                if ($SubCategoriesMappingData) :
                    foreach ($SubCategoriesMappingData as $kk => $vv) :
                        $subCategoriesID[] = $vv['category_id'];
                    endforeach;
                endif;
                $ArticleData['mainCategories'][$k]->subCategoriesID = $subCategoriesID;
            endforeach;
        endif;
        $SicknessSelectedList = [];
        foreach ($ArticleData->ArticlesSicknessMapping as $k => $v) :
            $SicknessSelectedList[] = $v['health_sickness_id'];
        endforeach;

        if (old('yahoo_ext_data')) :
            $old_yahoo_ext_arr = explode(',', old('yahoo_ext_data'));
        else :
            $old_yahoo_ext_arr = [];
        endif;
        $YahooExtSelectedList = [];
        if ($old_yahoo_ext_arr) :
            foreach ($old_yahoo_ext_arr as $k => $v) :
                $YahooExtSelectedList[] = $v;
            endforeach;
        else :
            foreach ($ArticleData->yahooRecommendations as $k => $v) :
                $YahooExtSelectedList[] = $v['articles_id'];
            endforeach;
        endif;

        if (old('further_reading')) :
            $old_further_reading_arr = explode(',', old('further_reading'));
        else :
            $old_further_reading_arr = [];
        endif;
        $FurtherList = [];
        if ($old_further_reading_arr) :
            foreach ($old_further_reading_arr as $k => $v) :
                $FurtherList[] = $v;
            endforeach;
        else :
            foreach ($ArticleData->recommendations as $k => $v) :
                $FurtherList[] = $v['articles_id'];
            endforeach;
        endif;
        // 取得資料庫資料 End

        // 主分類
        $CategoriesData = $this->MainCategoriesModel::where('categories_status', '1')
                                                        ->whereNot('categories_type', '3')
                                                        ->OrderBy('sort_index', 'desc')
                                                        ->select('categories_id', 'name')
                                                        ->get();
        foreach ($CategoriesData as $v) :
            $CategoriesDataList[$v['categories_id']] = $v['name'];
        endforeach;

        // 上稿者管理
        $AuthorsData = $this->AuthorsModel::where('status', true)
                                            ->get();
        foreach ($AuthorsData as $v) :
            $AuthorsDataList[$v['id']] = $v['name'];
        endforeach;

        // 文末廣宣
        $ArticleEndData = $this->EndTextModel::where(['status' => 1,'text_type' => 1])
                                            ->OrderBy('order_num', 'asc')
                                            ->get();

        // 取得醫學百科身體部位
        $HealthBody = $this->BodyModel::OrderBy('sort', 'asc')->get();
        foreach ($HealthBody as $v) :
            $HealthBodyList[$v['id']] = $v['name'];
        endforeach;

        // 取得醫學百科分類
        $HealthArticlesCategories = $this->ArticleCategoriesModel::where('status', 1)
                                                                    ->OrderBy('sort', 'asc')
                                                                    ->OrderBy('id', 'desc')
                                                                    ->get();
        $HealthArticlesCategoriesList = [
            0   => '請選擇'
        ];
        foreach ($HealthArticlesCategories as $v) :
            $HealthArticlesCategoriesList[$v['id']] = $v['name'];
        endforeach;

        foreach ($ArticleEndData as $v) :
            $ArticleEndDataList[$v['content']] = $v['short_title'];
        endforeach;
        $field = [
            'categories' => [
                'type'          => 'category_selectize',
                'title'         => '分類',
                'placeholder'   => '分類',
                'required'      => true,
                'options'       => $CategoriesDataList,
                'comment'       => null,
                'value'         => ''
            ],
            'medicine_article_sickness' => [
                'type'          => 'medicine_selectize',
                'title'         => '醫學百科疾病',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
            ],
            'medicine_article_category' => [
                'type'          => 'medicine_category_select',
                'title'         => '醫學百科分類',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => $HealthArticlesCategoriesList,
                'value'         => (old('medicine_article_category') ? old('medicine_article_category') : $ArticleData['medicine_article_category_id'])
            ],
            'publish'       => [
                'type'          => 'datetime',
                'title'         => '發佈時間',
                'placeholder'   => '發佈時間',
                'required'      => true,
                'comment'       => null,
                'value'         => (old('publish') ? old('publish') : $ArticleData['publish'])
            ],
            'title'         => [
                'type'          => 'text',
                'title'         => '文章標題',
                'placeholder'   => '文章標題',
                'required'      => true,
                'comment'       => null,
                'value'         => (old('title') ? old('title') : $ArticleData['title']),
            ],
            'og_title'      => [
                'type'          => 'text',
                'title'         => '社群標題',
                'placeholder'   => '社群標題',
                'required'      => true,
                'comment'       => null,
                'value'         => (old('og_title') ? old('og_title') : $ArticleData['og_title']),
            ],
            'seo_title'     => [
                'type'          => 'text',
                'title'         => 'SEO標題',
                'placeholder'   => 'SEO標題',
                'required'      => true,
                'comment'       => null,
                'value'         => (old('seo_title') ? old('seo_title') : $ArticleData['seo_title']),
            ],
            'author'        => [
                'type'          => 'selectize_author',
                'title'         => '上稿者名稱',
                'placeholder'   => '上稿者名稱',
                'required'      => true,
                'comment'       => null,
                'value'         => [(old('author') ? old('author') : $ArticleData['author']),(old('author_type') ? old('author_type') : $ArticleData['author_type'])],
            ],
            'adult_flag'    => [
                'type'          => 'radio',
                'title'         => '18禁文章',
                'placeholder'   => '18禁文章',
                'required'      => false,
                'comment'       => null,
                'value'         => (old('adult_flag') ? old('adult_flag') : $ArticleData['adult_flag'])
            ],
            'collaborator'   => [
                'type'          => 'select_collaborator',
                'title'         => '協作者',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'value'         => (old('collaborator') ? old('collaborator') : $ArticleData['collaborator']),
            ],
            'image'         => [
                'type'          => 'image',
                'title'         => '主圖 URL',
                'placeholder'   => '主圖 URL',
                'required'      => true,
                'comment'       => '建議圖片尺寸：1024 * 576 px',
                'value'         => [(old('image') ? old('image') : $ArticleData['image']), (old('image_alt') ? old('image_alt') : $ArticleData['image_alt'])],
            ],
            'ogimage'       => [
                'type'          => 'image',
                'title'         => '社群分享圖URL',
                'placeholder'   => '社群分享圖URL',
                'required'      => true,
                'comment'       => null,
                'value'         => [(old('ogimage') ? old('ogimage') : $ArticleData['ogimage']),null],
            ],
            'video_id'      => [
                'type'          => 'text',
                'title'         => '主影音',
                'placeholder'   => '輸入youtube 影音ID',
                'required'      => false,
                'comment'       => null,
                'value'         => (old('video_id') ? old('video_id') : $ArticleData['video_id']),
            ],
            'article_content'   => [
                'type'          => 'textarea',
                'title'         => '內容',
                'placeholder'   => '內容',
                'required'      => true,
                'comment'       => null,
                'value'         => (old('article_content') ? old('article_content') : $ArticleData['article_content']),
            ],
            'extended_reading'   => [
                'type'          => 'extended_text',
                'title'         => '插入看更多',
                'placeholder'   => '請輸入文章ID',
                'required'      => false,
                'comment'       => null,
            ],
            'further_reading'   => [
                'type'          => 'further_text',
                'title'         => '延伸閱讀',
                'placeholder'   => '延伸閱讀',
                'required'      => false,
                'comment'       => null,
                'value'         => implode(',', $FurtherList)
            ],
            'tag'           => [
                'type'          => 'selectize',
                'title'         => 'TAG',
                'placeholder'   => '限制 8 個 TAG',
                'required'      => false,
                'comment'       => null,
                'value'         => (old('tag') ? old('tag') : $ArticleData['tag'])
            ],
            'match_searchs' => [
                'type'          => 'selectize_text',
                'title'         => '字串搜尋',
                'placeholder'   => '單筆字數限制為30個字',
                'required'      => false,
                'comment'       => null,
                'value'         => (old('match_searchs') ? old('match_searchs') : $ArticleData['match_searchs']),
            ],
            'end_of_text' => [
                'type'          => 'end_select',
                'title'         => '文末廣宣',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => $ArticleEndDataList
            ],
            'talent_category_id' => [
                'type'          => 'master_select',
                'title'         => '醫師專家達人',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => [],
                'value'         => ($ArticleData->masters->isNotEmpty() ? [$ArticleData->masters[0]->type] : [])
            ],
            'articles_status'   => [
                'type'          => 'select',
                'title'         => '狀態',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => [1 => '發佈',0 => '下架'],
                'value'         => (old('articles_status') ? old('articles_status') : $ArticleData['articles_status']),
            ],
            'is_line_article'   => [
                'type'          => 'line_article_select',
                'title'         => 'LINE 文章供稿',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => [0 => '否', 1 => '是'],
                'value'         => [(old('is_line_article') ? old('is_line_article') : $ArticleData['is_line_article'])]
            ],
            'is_line_rss'   => [
                'type'          => 'line_select',
                'title'         => 'LINE 影音供稿',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => [0 => '否', 1 => '是'],
                'value'         => [(old('is_line_rss') ? old('is_line_rss') : $ArticleData['is_line_rss']),(old('video_file_name') ? old('video_file_name') : $ArticleData['video_file_name'])]
            ],
            'is_mixerbox_article'   => [
                'type'          => 'mixerbox_article_select',
                'title'         => 'MixerBox 供稿',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => [0 => '否', 1 => '是'],
                'value'         => [(old('is_mixerbox_article') ? old('is_mixerbox_article') : $ArticleData['is_mixerbox_article'])]
            ],
            'is_yahoo_rss'   => [
                'type'          => 'yahoo_select',
                'title'         => 'YAHOO供稿',
                'placeholder'   => '',
                'required'      => false,
                'comment'       => null,
                'options'       => [0 => '否', 1 => '是'],
                'value'         => (old('is_yahoo_rss') ? old('is_yahoo_rss') : $ArticleData['is_yahoo_rss'])
            ],
            'yahoo_ext'   => [
                'type'          => 'yahoo_ext_select',
                'title'         => 'YAHOO供稿延伸閱讀',
                'placeholder'   => '',
                'required'      => true,
                'comment'       => null,
                'options'       => [0 => '請選擇'],
                'value'         => $YahooExtSelectedList
            ]
        ];

        // 醫學百科ID
        $Encyclopedia = $this->EncyclopediaData;
        $Guangbian = $this->GuangbianData;
        if ($ArticlesCategoryType == 'ad') :
            unset($field['is_line_article'], $field['is_line_rss'], $field['is_mixerbox_article'], $field['is_yahoo_rss']);
            return view(
                'articles.ad.edit',
                compact('role_name', 'site_name', 'field', 'CategoriesDataList', 'AuthorsDataList', 'HealthBodyList', 'ArticleData', 'SicknessSelectedList', 'ArticlesCategoryType', 'id', 'Encyclopedia', 'Guangbian', 'LineArticleCondition', 'MixerboxArticleCondition')
            );
        else :
            return view(
                'articles.edit',
                compact('role_name', 'site_name', 'field', 'CategoriesDataList', 'AuthorsDataList', 'HealthBodyList', 'ArticleData', 'SicknessSelectedList', 'ArticlesCategoryType', 'id', 'Encyclopedia', 'Guangbian', 'LineArticleCondition', 'MixerboxArticleCondition')
            );
        endif;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        $LineArticleCondition = $this->LineArticleCondition;
        $MixerboxArticleCondition = $this->MixerboxArticleCondition;

        $ArticlesCategoryType = $request->input('type');

        $ArticleData = $this->ArticleModel::findOrFail($id);

        $validateRules = [
            'categories'    => ['array', new HasSicknessIdRule()],
            'categories.*'  => 'required|integer',
            'publish'       => 'required',
            'title'         => 'required',
            'og_title'      => 'required',
            'seo_title'     => 'required',
            'author'        => 'required',
            'author_type'   => 'required',
            'image'         => 'required',
            'ogimage'       => 'required',
            'article_content'   => 'required',
            'yahoo_ext_data'    => [new YahooExtDataRule($request->input('is_yahoo_rss'))],
            'video_id' => 'nullable|regex:/^[A-Za-z0-9_-]+$/',
            'is_line_article' => ['required_if:type,web',
                                    function ($attribute, $value, $fail) use ($request, $id, $LineArticleCondition) {
                                        if ($value == 1) {
                                            if (!array_key_exists($request->input('categories')[0], $LineArticleCondition)) {
                                                $fail('當:attribute為『是』時，只允許主分類為：' . implode("、", $LineArticleCondition));
                                            }
                                            if ($request->input('adult_flag') == 1) {
                                                $fail('當:attribute為『是』時，不能是18禁文章');
                                            }
                                            if ($request->input('articles_status') == 0) {
                                                $fail('當:attribute為『是』時，不能是下架狀態');
                                            }
                                        } else {
                                            $lineArticle = $this->LineArticlesModel::where('article_id', $id)->first();
                                            if ($lineArticle && $lineArticle->release_date) {
                                                $fail('此文章已供稿出去，無法將:attribute設置為否');
                                            }
                                        }
                                    },],
            'is_line_rss' => ['required_if:type,web',
                                function ($attribute, $value, $fail) use ($request) {
                                    if ($request->input('type') != 'ad' && $value == 1) {
                                        if ($request->input('adult_flag') == 1) {
                                            $fail('當:attribute為『是』時，不能是18禁文章');
                                        }
                                        if ($request->input('articles_status') == 0) {
                                            $fail('當:attribute為『是』時，不能是下架狀態');
                                        }
                                    }
                                },],
            'video_file_name' => [function ($attribute, $value, $fail) use ($request) {
                if (
                    $request->input('type') != 'ad'
                                        && $request->input('is_line_rss') == 1
                                        && empty($value)
                ) {
                    $fail('當Line影音供稿為『是』時，Line供稿影片為必填');
                }
            },],
            'is_mixerbox_article' => ['required_if:type,web',
                                        function ($attribute, $value, $fail) use ($request, $id, $MixerboxArticleCondition) {
                                            if ($value == 1) {
                                                if (!array_key_exists($request->input('categories')[0], $MixerboxArticleCondition)) {
                                                    $fail('當:attribute為『是』時，只允許主分類為：' . implode("、", $MixerboxArticleCondition));
                                                }
                                                if ($request->input('adult_flag') == 1) {
                                                    $fail('當:attribute為『是』時，不能是18禁文章');
                                                }
                                                if ($request->input('articles_status') == 0) {
                                                    $fail('當:attribute為『是』時，不能是下架狀態');
                                                }
                                            } else {
                                                $mixerboxArticle = $this->MixerboxArticlesModel::where('article_id', $id)->first();
                                                if ($mixerboxArticle && $mixerboxArticle->release_date) {
                                                    $fail('此文章已供稿出去，無法將:attribute設置為否');
                                                }
                                            }
                                        },],
            'is_yahoo_rss' => ['required_if:type,web',
                                function ($attribute, $value, $fail) use ($request, $id) {
                                    if ($request->input('type') != 'ad' && $value == 1) {
                                        if ($request->input('adult_flag') == 1) {
                                            $fail('當:attribute為『是』時，不能是18禁文章');
                                        }
                                        if ($request->input('articles_status') == 0) {
                                            $fail('當:attribute為『是』時，不能是下架狀態');
                                        }
                                    }

                                    if ($request->input('type') != 'ad' && $value == 0) {
                                        $yahooArticle = $this->YahooRssModel::where('article_id', $id)->first();
                                        if ($yahooArticle && $yahooArticle->rss_release_date) {
                                            $fail('此文章已供稿出去，無法將:attribute設置為否');
                                        }
                                    }
                                },],
            'talent_category_id' => ['required_if:master_type,1,2,3', function ($attribute, $value, $fail) use ($request) {
                if ($value < 1 && $request->master_type > 0) {
                    $fail('請選擇醫師專家達人姓名');
                }
            },],
        ];

        $validateAttributes = [
            'categories.*'  => '分類',
            'publish'       => '發佈時間',
            'title'         => '文章標題',
            'medicine_article_sickness_id'  => '醫學百科疾病',
            'medicine_article_category'     => '醫學百科分類',
            'og_title'      => '社群標題',
            'seo_title'     => 'SEO標題',
            'author'        => '上稿者名稱',
            'author_type'   => '上稿者分類',
            'adult_flag' => '18禁文章',
            'image'         => '主圖URL',
            'ogimage'       => '社群分享圖URL',
            'article_content'   => '內容',
            'is_line_article'       => 'Line文章供稿',
            'is_line_rss'       => 'Line影音供稿',
            'video_file_name'   => 'Line供稿影片',
            'is_mixerbox_article'   => 'MixerBox供稿',
            'is_yahoo_rss' => 'YAHOO供稿',
            'master_type' => '醫師專家達人',
            'talent_category_id' => '專家',
        ];

        $validateMessages = [
            'required_if' => '當『:other』欄位為『是』時，『:attribute』為必填',
            'video_id.regex' => '主影音欄位，請輸入正確的Youtube參數(可輸入規則：大小寫英文、數字、底線、破折號)',
            'talent_category_id.required_if' => '請選擇醫師專家達人姓名',
        ];

        $this->validate($request, $validateRules, $validateMessages, $validateAttributes);

        $PostData = $request->all();

        try {
            DB::transaction(function () use ($PostData, $ArticleData, $ArticlesCategoryType) {
                // 新增文章，產生Article_ID
                $HealthArticleData = [
                    'articles_status'       => $PostData['articles_status'],
                    'publish'               => $PostData['publish'],
                    'title'                 => $PostData['title'],
                    'og_title'              => $PostData['og_title'],
                    'seo_title'             => $PostData['seo_title'],
                    'author'                => $PostData['author'],
                    'author_type'           => $PostData['author_type'],
                    'medicine_article_category_id'  => $PostData['medicine_article_category'],
                    'adult_flag'            => $PostData['adult_flag'],
                    'image'                 => $PostData['image'],
                    'image_alt'             => ($PostData['image_alt'] ? $PostData['image_alt'] : ''),
                    'ogimage'               => $PostData['ogimage'],
                    'video_id'              => ($PostData['video_id'] ? $PostData['video_id'] : ''),
                    'fb_ia_video'           => '',
                    'tag'                   => ($PostData['tag'] ? $PostData['tag'] : ''),
                    'match_searchs'         => ($PostData['match_searchs'] ? $PostData['match_searchs'] : ''),
                    'extended_article'      => ($PostData['further_reading'] ? $PostData['further_reading'] : ''),
                    'article_content'       => $PostData['article_content'],
                    'is_line_article'       => isset($PostData['is_line_article']) && $PostData['is_line_article'] ? $PostData['is_line_article'] : 0,
                    'is_line_rss'           => isset($PostData['is_line_rss']) && $PostData['is_line_rss'] ? $PostData['is_line_rss'] : 0,
                    'video_file_name'       => isset($PostData['video_file_name']) && $PostData['video_file_name'] ? $PostData['video_file_name'] : '',
                    'is_mixerbox_article'   => isset($PostData['is_mixerbox_article']) && $PostData['is_mixerbox_article'] ? $PostData['is_mixerbox_article'] : 0,
                    'is_yahoo_rss'          => isset($PostData['is_yahoo_rss']) && $PostData['is_yahoo_rss'] ? $PostData['is_yahoo_rss'] : 0,
                    'collaborator'          => $PostData['collaborator'] ?? null,
                    'created_user'          => auth()->user()->name,
                    'updated_user'          => auth()->user()->name,
                    'match_url'             => ''
                ];
                $ArticlesInsertData = $ArticleData->update($HealthArticleData);
                $ArticleId = $ArticleData['articles_id'];

                // 醫師專家
                $ArticleData->masters()->detach();
                $PostData['talent_category_id'] = array_filter($PostData['talent_category_id']);
                if (!empty($PostData['talent_category_id'])) {
                    $ArticleData->masters()->sync($PostData['talent_category_id']);
                }

                // 新增文章對應分類
                $this->ArticleCategoriesMappingsModel::where('article_id', $ArticleId)->delete();
                if ($ArticlesCategoryType == 'ad') :
                    $CategoriesData = [
                        0   => $this->GuangbianData['categories_id']
                    ];
                    $SubCategoriesData = [
                        0   => ['']
                    ];
                else :
                    $CategoriesData = [];
                    $SubCategoriesData = [];
                endif;

                foreach ($PostData['categories'] as $k => $v) :
                    if (!in_array($v, $CategoriesData)) :
                        $CategoriesData[] = $v;
                    endif;
                    if ($PostData['subcategories'][$k]) :
                        $SubCategoriesData[$v][] = $PostData['subcategories'][$k];
                    endif;
                endforeach;
                foreach ($CategoriesData as $k => $v) :
                    if ($v) :
                        $ArticleMappingCategoriesData = [
                            'article_id'    => $ArticleId,
                            'category_id'   => $v,
                            'sort'          => $k
                        ];
                        $this->ArticleCategoriesMappingsModel::create($ArticleMappingCategoriesData);
                    endif;
                endforeach;
                $i = 0;
                foreach ($SubCategoriesData as $k => $v) :
                    foreach ($v as $vv) :
                        if ($vv) :
                            $ArticleMappingCategoriesData = [
                                'article_id'    => $ArticleId,
                                'category_id'   => $vv,
                                'sort'          => $i,
                                'parent'        => $k
                            ];
                            $this->ArticleCategoriesMappingsModel::create($ArticleMappingCategoriesData);
                            $i++;
                        endif;
                    endforeach;
                endforeach;

                // 新增文章對應疾病
                $this->ArticlesSicknessModel::where('article_id', $ArticleId)->delete();

                $SicknessArrData = explode(',', $PostData['medicine_article_sickness_id']);
                foreach ($SicknessArrData as $k => $v) :
                    if ($v) :
                        $SicknessData = [
                            'article_id'            => $ArticleId,
                            'health_sickness_id'    => $v,
                            'created_user'          => auth()->user()->name,
                            'updated_user'          => auth()->user()->name
                        ];
                        $this->ArticlesSicknessModel::create($SicknessData);
                    endif;
                endforeach;

                // 延伸閱讀
                $this->ArticlesFurtherReadingModel::where('article_id', $ArticleId)->where('type', 'article')->delete();

                $FurtherReadingArrData = explode(',', $PostData['further_reading']);
                foreach ($FurtherReadingArrData as $v) :
                    if ($v) :
                        $FurtherReadingData = [
                            'article_id'                => $ArticleId,
                            'recommendation_article_id' => $v,
                            'type'                      => 'article',
                            'created_user'              => auth()->user()->name,
                            'updated_user'              => auth()->user()->name
                        ];
                        $this->ArticlesFurtherReadingModel::create($FurtherReadingData);
                    endif;
                endforeach;

                // Tag
                $this->ArticleTagMappingModel::where('article_id', $ArticleId)->delete();

                $TagArrData = explode(',', $PostData['tag']);
                foreach ($TagArrData as $v) :
                    if ($v) :
                        $TagData = [
                            'article_id'    => $ArticleId,
                            'tag'           => trim($v),
                            'created_user'  => auth()->user()->name,
                            'updated_user'  => auth()->user()->name
                        ];
                        $this->ArticleTagMappingModel::create($TagData);
                    endif;
                endforeach;

                //Line 文章供稿
                if ($HealthArticleData['is_line_article'] == 1) {
                    $lineArticle = $this->LineArticlesModel::where('article_id', $ArticleData['articles_id'])->first();
                    if (!$lineArticle) {
                        $this->LineArticlesModel::create([
                            'article_id' => $ArticleData['articles_id'],
                            'created_user'  => auth()->user()->name,
                            'updated_user'  => auth()->user()->name
                        ]);
                    }
                } else {
                    $this->LineArticlesModel::where('article_id', $ArticleId)->delete();
                }

                //MixerBox 供稿
                if ($HealthArticleData['is_mixerbox_article'] == 1) {
                    $mixerboxArticle = $this->MixerboxArticlesModel::where('article_id', $ArticleData['articles_id'])->first();
                    if (!$mixerboxArticle) {
                        $this->MixerboxArticlesModel::create([
                            'article_id' => $ArticleData['articles_id'],
                            'created_user'  => auth()->user()->name,
                            'updated_user'  => auth()->user()->name
                        ]);
                    }
                } else {
                    $this->MixerboxArticlesModel::where('article_id', $ArticleId)->delete();
                }


                if ($HealthArticleData['is_yahoo_rss'] == 1) {
                    $yahooArticle = $this->YahooRssModel::where('article_id', $ArticleId)->first();

                    if ($yahooArticle && !$yahooArticle->rss_release_date) {
                        $this->ArticlesFurtherReadingModel::where('article_id', $ArticleId)->where('type', 'yahoo')->delete();
                        $YahooExtArrData = explode(',', $PostData['yahoo_ext_data']);
                        foreach ($YahooExtArrData as $v) :
                            if ($v) :
                                $YahooExtData = [
                                    'article_id'                => $ArticleId,
                                    'recommendation_article_id' => $v,
                                    'type'                      => 'yahoo',
                                    'created_user'              => auth()->user()->name,
                                    'updated_user'              => auth()->user()->name
                                ];
                                $this->ArticlesFurtherReadingModel::create($YahooExtData);
                            endif;
                        endforeach;
                    }

                    if (!$yahooArticle) {
                        $YahooExtArrData = explode(',', $PostData['yahoo_ext_data']);
                        foreach ($YahooExtArrData as $v) :
                            if ($v) :
                                $YahooExtData = [
                                    'article_id'                => $ArticleId,
                                    'recommendation_article_id' => $v,
                                    'type'                      => 'yahoo',
                                    'created_user'              => auth()->user()->name,
                                    'updated_user'              => auth()->user()->name
                                ];
                                $this->ArticlesFurtherReadingModel::create($YahooExtData);
                            endif;
                        endforeach;

                        $this->YahooRssModel::create(
                            [
                                'article_id' => $ArticleId,
                                'created_user' => auth()->user()->name,
                                'updated_user' => auth()->user()->name
                            ]
                        );
                    }
                } else {
                    $this->ArticlesFurtherReadingModel::where('article_id', $ArticleId)->where('type', 'yahoo')->delete();
                    $this->YahooRssModel::where('article_id', $ArticleId)->delete();
                }
            });

            // todo: 同步資料
            event(new ArticleUpdated($ArticleData));

            if ($ArticlesCategoryType == 'ad') :
                parse_str($_COOKIE['article_ad_query_string'] ?? '', $queryArray);
                return redirect()->route('articles_ad.index', $queryArray)
                    ->with('success', '儲存成功');
            else :
                parse_str($_COOKIE['article_query_string'] ?? '', $queryArray);
                return redirect()->route('articles.index', $queryArray)
                    ->with('success', '儲存成功');
            endif;
        } catch (\Throwable $th) {
            $err = $th->getMessage();
            Log::alert($err);

            if ($ArticlesCategoryType == 'ad') :
                return redirect()->route('articles_ad.edit', $id)
                            ->withErrors($err)
                            ->withInput();
            else :
                return redirect()->route('articles.edit', $id)
                            ->withErrors($err)
                            ->withInput();
            endif;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id): Response
    {
        // Delete the resource with $id
        // ...

        // Return the existing response object
        return new Response('Resource deleted successfully', Response::HTTP_OK);
    }

    /**
     * @param ArticlesAppendCategories $request
     * @return Response
     */
    public function appendCategories(ArticlesAppendCategories $request): Response
    {
        $data = $request->validated();

//        $articles = $this->service->filterArticles($data);

        return response()->json([]);
    }

     /**
     * @param main_category $main_category
     * @param sub_category $sub_category
     * @return View
     */
    public function searchArticles(): View
    {
        $master = isset($_GET['master']) ? $_GET['master'] : '';
        $main_category = isset($_GET['main_category']) ? $_GET['main_category'] : '';
        $sub_category = isset($_GET['sub_category']) ? $_GET['sub_category'] : '';

        $page_limit = 12;
        $cond = [];
        if (!empty($master)) {
            $cond['master'] = $master;
        }
        if (!empty($main_category)) {
            $cond['main_category'] = $main_category;
        }
        if (!empty($sub_category)) {
            $cond['sub_category'] = $sub_category;
        }

        $results = ArticleModel::when(!empty($master), function ($query) use ($master) {
                                    $query->whereHas('masters', function ($query) use ($master) {
                                        $query->where('type', $master);
                                    });
        })
                                ->when(!empty($sub_category) && !empty($main_category), function ($query) use ($sub_category, $main_category) {
                                    $query->whereHas('subCategories', function ($query) use ($sub_category, $main_category) {
                                        $query->where('category_id', $sub_category)
                                                ->where('parent', $main_category) //是否要加條件為主分類才出現
                                                ->groupBy('article_id');
                                    });
                                })
                                ->when(!empty($main_category) && empty($sub_category), function ($query) use ($main_category) {
                                    $query->whereHas('mainCategories', function ($query) use ($main_category) {
                                        $query->where('category_id', $main_category)
                                                ->whereNull('parent')
                                                // ->where('sort', 0) //是否要加條件為主分類才出現
                                                ->whereNot('categories_type', MainCategoriesType::ADVERTORIAL)
                                                ->groupBy('article_id');
                                    });
                                })
                                ->active()
                                ->orderBy('publish', 'desc')
                                ->paginate($page_limit)
                                ->appends($cond);

        return view('articles.searchArticles', [
            'results' => $results
        ]);
    }

    /**
     * @param ArticlesAjaxCategoriesChange $request
     * @return Response
     */
    public function changeCategories(Request $request): Response
    {
        $categories_id = $request['categories_id'];

        $CategoriesData = MainCategoriesModel::where('categories_id', $categories_id)->first();
        $SubCategoriesData = $CategoriesData->subCategories->where('status', 1)->values();

        return response()->json($SubCategoriesData);
    }

    /**
     * @param ArticlesAjaxCategoriesChange $request
     * @return Response
     */
    public function ContentExtendReading($exclude_id, Request $request): Response
    {
        $errMsg = '';
        $keywords = '';
        $page = 1;
        $pagesize = 10;

        // 確認請求參數是否合法，並從中取得關鍵字
        if (isset($request['keywords'])) {
            $keywords = urldecode($request->input('keywords'));
            $NowDataList = json_decode($request->input('nowjson')) ? json_decode($request->input('nowjson')) : [];
        } else {
            $errMsg = '請傳入正確資料';
            return response()->json(['errMsg' => $errMsg], 400);
        }

        // 如果有分頁參數，設定取出頁數；否則預設為第一頁
        if (isset($request['page'])) {
            $page = $request->input('page');
        }

        // 取得擴展閱讀文章資料，如果失敗回傳錯誤訊息
        $ExtendArticlesData = $this->ExtendArticles($exclude_id, $keywords, $pagesize);
        if (!$ExtendArticlesData) {
            $errMsg = '系統錯誤';
            return response()->json(['errMsg' => $errMsg], 400);
        }

        foreach ($ExtendArticlesData as $key => $value) :
            if (in_array($value['articles_id'], $NowDataList)) :
                $ExtendArticlesData[$key]['isCheck'] = true;
            else :
                $ExtendArticlesData[$key]['isCheck'] = false;
            endif;
        endforeach;

        // 為擴展閱讀文章資料產生分頁連結
        $ExtendArticlesData->appends([
            'keywords'  => $keywords
        ])->links();

        // 回傳擴展閱讀文章資料
        return response()->json($ExtendArticlesData);
    }

    public function ChangeExtendedReading(Request $request): Response
    {
        if (!$request->query('articles_ids')) :
            $errMsg = '請傳入ID';
            return response()->json(['errMsg' => $errMsg], 400);
        endif;
        $articles_arr = explode(',', $request->query('articles_ids'));
        $ArticleDatas = ArticleModel::whereIn('articles_id', $articles_arr)->get();
        return response()->json($ArticleDatas);
    }

    public function GetFurtherData(Request $request): Response
    {
        if ($request->input('categories_id')) :
            $main_category_id = $request->input('categories_id');
        else :
            $main_category_id = [];
        endif;
        // $FurtherUrl = env("FURTHER_URL","http://34.80.91.60:5050/health/post_recommend");
        // $form_data = $request->input('form_data');
        // foreach($form_data as $k=>$v):
        //     $data[$v['name']] = $v['value'];
        // endforeach;
        // try {
        //     $sendData = array(
        //         'text'  =>  strip_tags($data['article_content'])
        //     );
        //     $postData = Http::timeout(5)->post($FurtherUrl, $sendData);
        //     $http_result = array(
        //         'status'    =>  $postData->status(),
        //         'data'      =>  $postData->json()
        //     );
        // } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $http_result = ['status' => false];
        // }
        if ($http_result['status']) :
            $result = explode(',', $http_result['data']);
            if (!is_array($result)) :
                $result = [];
            elseif (!is_numeric($result[0])) :
                $result = [];
            endif;
        else :
            $result = [];
        endif;
        // 介接id處理
        $return_data = [
            'ids'       => implode(',', $result),
            'htmlData'  => '<p>延伸閱讀資料如下：</p>'
        ];
        foreach ($result as $v) :
            $data = ArticleModel::where('articles_id', $v)->first();
            if ($data) :
                $return_data['htmlData'] .= '<p style="color:blue"><a href="javascript:;" target="_blank">' . $v . ' ' . $data->title . '</a></p>';
            endif;
        endforeach;
        if ($return_data['htmlData'] == '<p>延伸閱讀資料如下：</p>') :
            // 熱門文章
            $random_articles = $this->service->getFurtherData(20, 6, ($request->input('article_id') ? [$request->input('article_id')] : []), $main_category_id);

            // 熱門文章 End
            $return_data['htmlData'] = '<p>熱門推薦資料如下：</p>';
            $ids = '';
            foreach ($random_articles as $v) :
                if ($ids) :
                    $ids = $ids . ',' . $v['articles_id'];
                else :
                    $ids = $v['articles_id'];
                endif;
                $return_data['htmlData'] .= '<p style="color:blue"><a href="javascript:;" target="_blank">' . $v['articles_id'] . ' ' . $v['title'] . '</a></p>';
            endforeach;
            $return_data['ids'] = $ids;
        endif;
        return response()->json($return_data);
    }

    public function getFurtherLoading(Request $request): Response
    {
        $FurtherIds = $request['further_reading'];
        if ($FurtherIds) :
            $resultData = [];
            foreach (explode(',', $FurtherIds) as $k => $v) :
                $resultData[] = $this->ArticleModel::where('articles_id', $v)->first();
            endforeach;
            return response()->json($resultData);
        else :
            return response()->json([]);
        endif;
    }

    // AI推薦tagFor數據Team
    public function getTagRecomment(Request $request): Response
    {
        // Set the API URL
        $tagUrl = env('TAG_RECOMMEND_URL', 'http://34.80.91.60:5050/health/post_tag_recommend');

        try {
            // Send HTTP POST request to the API
            $postData = Http::timeout(10)->post($tagUrl, [
                'article' => strip_tags($request->input('article')),
            ]);
            if ($postData->status()) :
                $recomment_tag = $postData->json()['recomment_tag'];
                $recomment_tag_arr = array_unique(explode(',', $recomment_tag));
                $recomment_tag = implode(',', $recomment_tag_arr);
            else :
                $recomment_tag = '';
            endif;
            $result['recomment_tag'] = $recomment_tag;

            // Return JSON response with the API status and data
            return response()->json([
                'status' => $postData->status(),
                'data' => $result,
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Return JSON response with status false if the API request failed
            return response()->json([
                'status' => false,
            ]);
        }
    }

    // AI推薦tagForChatGPT
    public function getTagRecommentForChatGPT(Request $request): Response
    {
        // Set OpenAI API credentials and endpoint URL
        $apiKey = env('AI_KEY', '');
        $url = env('AI_URL', '');

        try {
            // Send HTTP POST request to the OpenAI API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post($url, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'user',
                        "content" => "請依照下列條件推薦最熱門之關鍵字，條件如下：\n1. 僅能從此內文中挑出對應關鍵字，內文：'" . strip_tags($request->input('article')) . "'\n 2. 參考網站：https://health.tvbs.com.tw\n 3. 每筆關鍵字字數為3個字以內\n 4. 總筆數最多10筆"
                    ],
                ],
                'temperature' => 0.5,
            ]);

            // Decode JSON response from the OpenAI API
            $responseData = $response->json();

            // Find the response message with numbered list
            $message = '';
            if (!empty($responseData['choices'])) {
                foreach ($responseData['choices'] as $choice) {
                    $message = $choice['message']['content'];
                }
            }
            preg_match_all('/\d+\.\s+(.*)\n/', $message, $matches);
            $keywords = implode(',', $matches[1]);

            // Return JSON response with the recommended tags
            return response()->json([
                'status' => 200,
                'data' => [
                    'recomment_tag' => $keywords,
                ],
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Return JSON response with status false if the API request failed
            return response()->json([
                'status' => false,
            ]);
        }
    }

    /**
     *  健康2.0 取得相關文章
     *
     */
    public function getHealthCategoryArticles(Request $request): Response
    {
        // 從請求參數中取得分類 ID 陣列
        $array_categories_ids = explode('-', $request->input('category'));
        $articles_id = $request->input('articles_id');

        // 如果分類 ID 陣列為空，回應錯誤訊息
        if (empty($array_categories_ids)) {
            $errMsg = '請傳入所選分類';
            return response()->json(['errMsg' => $errMsg], 400);
        }

        // 查詢符合條件的文章資料
        $articles = DB::table('health_articles as ha')
            ->join('health_article_categories_mappings as hacm', 'ha.articles_id', '=', 'hacm.article_id')
            ->join('health_categories as hc', 'hacm.category_id', '=', 'hc.categories_id')
            ->select('ha.articles_id', 'ha.title')
            ->where([
                ['ha.articles_status', '=', 1],  // 文章狀態需為已發布
                ['hc.categories_type', '<>', 3], // 分類類型不能為「問答」
                ['ha.publish', '<=', Carbon::now()], // 發佈日期需早於現在時間
            ])
            ->when($articles_id, function ($query, $articles_id) {
                return $query->where('ha.articles_id', '!=', $articles_id);
            })
            ->whereIn('hacm.category_id', $array_categories_ids) // 分類 ID 需符合請求參數中的值
            ->orderByDesc('ha.publish') // 按照發佈日期遞減排序
            ->limit(50) // 取出前 50 筆
            ->get()
            ->toArray(); // 轉換為陣列格式

        // 將文章資料轉換為單一陣列形式（去除 Laravel 所產生的額外屬性）
        $articles = $this->arrToOne($articles);

        // 使用自訂排序函數對文章資料進行排序
        uasort($articles, [__CLASS__, 'sort_by_health_category_articles']);

        // 回傳文章資料
        return response()->json($articles);
    }

    public function getArticlesForYahoo(Request $request): Response
    {
        $articles_arr = explode(',', $request['articles_id']);
        $list_str = "";
        foreach ($articles_arr as $v) :
            $ArticleData = ArticleModel::where('articles_id', $v)->first();
            if ($ArticleData) :
                $list_str = $list_str . "<p><a href='javascript:;' class='removeArticle' ids='" . $v . "'>" . $ArticleData['title'] . "</a></p>";
            endif;
        endforeach;
        return response()->json($list_str);
    }

    public function ExtendArticles($exclude_id = '', $keywords = '', $limit = 10)
    {
        if (!$keywords || !($keywords = preg_split('/[,\s]/', $keywords, -1, PREG_SPLIT_NO_EMPTY))) {
            return false;
        }
        $ArticlesData = ArticleModel::with(['mainCategories'])
            ->where('articles_status', 1)
            ->whereNot('articles_id', $exclude_id)
            ->whereHas('mainCategories', function ($query) {
                $query->whereNot('categories_type', 3);
            })
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->orWhere('title', 'like', "%{$keyword}%");
                        $query->orWhere('article_content', 'like', "%{$keyword}%");
                        $query->orWhere('tag', 'like', "%{$keyword}%");
                    });
                }
            })
            ->orderByDesc('publish')
            ->paginate($limit);

        return $ArticlesData;
    }

    public function GetHealthOrgans(Request $request): Response
    {
        if ($request['body_id']) :
            $OrgansData = $this->OrgansModel::where('body_id', $request['body_id'])->get();
            return response()->json($OrgansData);
        else :
            $errMsg = '無傳入id';
            return response()->json(['errMsg' => $errMsg], 400);
        endif;
    }

    public function GetHealthSickness(Request $request): Response
    {
        if ($request['organs_id']) :
            $SicknessToOrganData = $this->SicknessToOrganModel::where('organ_id', $request['organs_id'])->get();
            $SicknessData = [];
            foreach ($SicknessToOrganData as $k => $v) :
                $SicknessData[] = $v->SicknessToOrgan;
            endforeach;
            return response()->json($SicknessData);
        else :
            $errMsg = '無傳入id';
            return response()->json(['errMsg' => $errMsg], 400);
        endif;
    }

    public function LoadArticleSickness(Request $request): Response
    {
        if ($request['sickness_id']) :
            $sickness_id_array = explode(',', $request['sickness_id']);
            $SickDataList = [];
            foreach ($sickness_id_array as $k => $v) :
                $SickDataList[] = $this->SicknessModel::where('id', $v)->first();
            endforeach;
            return response()->json($SickDataList);
        else :
            return response()->json([]);
        endif;
    }

    public function GetMastersList(Request $request): Response
    {
        if ($request['master_type']) :
            $MastersListData = $this->MastersModel::with(['institution'])->where('type', $request['master_type'])->where('status', '1')->get();
            return response()->json($MastersListData);
        else :
            return response()->json([]);
        endif;
    }

    private function arrToOne($multi)
    {
        $arr = [];
        foreach ($multi as $key => $val) {
            if (is_array($val)) {
                $arr = array_merge($arr, $this->arrToOne($val));
            } else {
                $arr[] = $val;
            }
        }
        return $arr;
    }

    private static function sort_by_health_category_articles($a, $b)
    {
        if ($a->articles_id == $b->articles_id) {
            return 0;
        }
        return ($a->articles_id < $b->articles_id) ? 1 : -1;
    }
}
