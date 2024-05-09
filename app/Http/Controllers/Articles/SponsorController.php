<?php

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\DateRangeRequest;

use App\Models\Articles\SponsorAdModel;
use App\Models\Categories\MainCategoriesModel;
use App\Models\Articles\ArticleModel;
use DB;

class SponsorController extends Controller
{
    /** @var string */
    private $role_name = 'sponsorlist';
    private $site_name = 'sponsorlist';

    protected $validateRules = [
        'sponsor_id' => 'required|integer',
        'start' => 'date|before:end',
        'end' => 'date|after:start'
    ];

    protected $validateAttributes = [
        'sponsor_id' => '廣編稿ID',
        'start' => '開始時間',
        'end' => '結束時間'
    ];

    public $editField = [
        [
            'title' => '廣編稿文章ID匯入',
            'type' => 'text',
            'name' => 'article_id',
            'placeholder' => '',
            'required' => false,
            'id' => 'article_id',
            'class' => ''
        ],
        [
            'title' => '開始時間',
            'type' => 'datetime',
            'name' => 'start',
            'placeholder' => '',
            'required' => TRUE,
            'id' => 'start',
            'class' => ''
        ],
        [
            'title' => '結束時間',
            'type' => 'datetime',
            'name' => 'end',
            'placeholder' => '',
            'required' => TRUE,
            'id' => 'end',
            'class' => ''
        ]
    ];

    /**
     * ArticlesController constructor.
     * @param ArticlesService $service
     */
    public function __construct(
    	SponsorAdModel $SponsorAdModel,
    	MainCategoriesModel $MainCategoriesModel,
        ArticleModel $ArticleModel
    )
    {
        $this->SponsorAdModel = $SponsorAdModel;
        $this->MainCategoriesModel = $MainCategoriesModel;
        $this->ArticleModel = $ArticleModel;
    }

     /**
     * Display a listing of the resource.
     *
     * @param ArticlesIndex $request
     * @return View
     */
    public function index(Request $request): View
    {
        $category_id = $request->query('searchMainCategories');
        $categories_type = 1;
        if($request->query('searchSubCategories')):
            $category_id = $request->query('searchSubCategories');
            $categories_type = 2;
        endif;
        if(!$category_id):
            $category_id = 0;
        endif;

        $role_name = $this->role_name;
        $perPage = 20;
        $MainCategoriesList = $this->MainCategoriesModel::with('subCategories')
        												->where('categories_type','!=','3')
        												->where('categories_status','1')
        												->OrderBy('sort_index','asc')
        												->get();
        
        $datas = $this->SponsorAdModel::with('article')
                                    ->where('categories_list_id', $category_id)
                                    ->OrderBy('position','asc')
                                    ->paginate($perPage);
        if($datas->total() == 0):
            $InputData = array(
                0   =>  [
                    'article_id'            =>  '0',
                    'categories_type'       =>  $categories_type,
                    'categories_list_id'    =>  $category_id,
                    'position'              =>  3,
                    'start'                 =>  date('Y-m-d H:i:s'),
                    'end'                   =>  date('Y-m-d H:i:s', strtotime("+1 month")),
                    'created_user'          =>  auth()->user()->name,
                    'updated_user'          =>  auth()->user()->name
                ],
                1   =>  [
                    'article_id'            =>  '0',
                    'categories_type'       =>  $categories_type,
                    'categories_list_id'    =>  $category_id,
                    'position'              =>  8,
                    'start'                 =>  date('Y-m-d H:i:s'),
                    'end'                   =>  date('Y-m-d H:i:s', strtotime("+1 month")),
                    'created_user'          =>  auth()->user()->name,
                    'updated_user'          =>  auth()->user()->name
                ]
            );
            foreach($InputData as $key=>$value):
                $datas[] = $this->SponsorAdModel::create($value);
            endforeach;
        elseif($datas->total() == 1):
            foreach($datas as $key=>$value):
                if($value['position'] == 3):
                    $insertData = [
                        'article_id'            =>  '0',
                        'categories_type'       =>  $categories_type,
                        'categories_list_id'    =>  $category_id,
                        'position'              =>  8,
                        'start'                 =>  date('Y-m-d H:i:s'),
                        'end'                   =>  date('Y-m-d H:i:s', strtotime("+1 month")),
                        'created_user'          =>  auth()->user()->name,
                        'updated_user'          =>  auth()->user()->name
                    ];
                    $datas[] = $this->SponsorAdModel::create($insertData);
                elseif($value['position'] == 8):
                    $insertData = [
                        'article_id'            =>  '0',
                        'categories_type'       =>  $categories_type,
                        'categories_list_id'    =>  $category_id,
                        'position'              =>  3,
                        'start'                 =>  date('Y-m-d H:i:s'),
                        'end'                   =>  date('Y-m-d H:i:s', strtotime("+1 month")),
                        'created_user'          =>  auth()->user()->name,
                        'updated_user'          =>  auth()->user()->name
                    ];
                    $datas[] = $this->SponsorAdModel::create($insertData);
                endif;
            endforeach;
        endif;

        $field = ['位置', '廣編稿主圖', '廣編稿標題', '發佈時間', '下架時間', '廣編狀態', '編輯'];
        $editField = $this->editField;

        return view(
        	'articles.sponsorlist.index',
        	compact('role_name', 'MainCategoriesList', 'datas', 'field', 'editField')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(request $request): View
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function show(Request $request, $id): View
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result =  $this->SponsorAdModel::find($id);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(DateRangeRequest $request, $id): Response
    {
        $this->validate($request, $this->validateRules, [], $this->validateAttributes);
        $InputData = $request->input();
        $SponsorData = $this->SponsorAdModel::find($InputData['sponsor_id']);
        if($InputData['article_id'] > 0):
            $ArticleData = $this->ArticleModel::with('mainCategories')
                                            ->with('subCategories')
                                            ->where('articles_id', $InputData['article_id'])
                                            ->first();
            $is_Sponsor_Article = false;
            $isCategoriesArticle = false;
            foreach($ArticleData['mainCategories'] as $key=>$value):
                if($value['categories_type'] == 3):
                    $is_Sponsor_Article = true;
                endif;
                if($SponsorData['categories_type'] == 1 && $SponsorData['categories_list_id'] == $value['categories_id']):
                    $isCategoriesArticle = true;
                endif;
            endforeach;
            foreach($ArticleData['subCategories'] as $key=>$value):
                if($SponsorData['type'] == 2 && $SponsorData['categories_list_id'] == $value['sub_categories_id']):
                    $isCategoriesArticle = true;
                endif;
            endforeach;
            if($SponsorData['categories_type'] == 1 && $SponsorData['categories_list_id'] == 0):
                $isCategoriesArticle = true;
            endif;

            if(!$is_Sponsor_Article):
                return response()->json(['errors'=>['請選擇『文章總覽-廣告業務』之文章']], 422);
            endif;
            if(!$isCategoriesArticle):
                return response()->json(['errors'=>['該文章不屬於此分類，請再重新確認']], 422);
            endif;
        endif;

        $UpdateData = [
            'article_id'    =>  ($request->input('article_id')?$request->input('article_id'):0),
            'start'         =>  $request->input('start'),
            'end'           =>  $request->input('end'),
            'updated_user'  =>  auth()->user()->name
        ];
        $SponsorData->update($UpdateData);

        return response()->json($request->input(), 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id): Response
    {
        //
    }
}
