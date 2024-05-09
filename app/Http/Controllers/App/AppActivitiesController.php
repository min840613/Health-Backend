<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\App\AppActivitiesModel;
use App\Models\Articles\ArticleModel;
use Session;
use App\Helpers\ImageHelper;
use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;

class AppActivitiesController extends Controller
{
    protected AppActivitiesModel $AppActivitiesModel;
    protected ArticleModel $ArticleModel;

    private $role_name = 'app_activities';
    private $site_name = 'app_activities';

    public $select_type = [
        'title' => '狀態切換',
        'type' => 'select',
        'name' => 'select_type',
        'placeholder' => '',
        'value' => 1,
        'required' => TRUE,
        'id' => 'select_type',
        'option' => [
            1 => '上架活動',
            0 => '下架活動'
        ]
    ];

    public $searchField = [
        [
            'title' => '活動名稱',
            'type' => 'text',
            'name' => 'search_title',
            'placeholder' => '請輸入活動名稱',
            'required' => FALSE,
            'id' => 'search_title'
        ],
        [
            'title' => '活動區間',
            'type' => 'datetime',
            'name' => 'datetime',
            'elements' =>
            [
                'start' => [
                    'title' => '',
                    'type' => 'datetime',
                    'name' => 'search_start',
                    'placeholder' => '請輸入活動上架時間',
                    'required' => FALSE,
                    'id' => 'search_start',
                    'class' => 'col-md-12'
                ],
                'end' => [
                    'title' => '',
                    'type' => 'datetime',
                    'name' => 'search_end',
                    'placeholder' => '請輸入活動下架時間',
                    'required' => FALSE,
                    'id' => 'search_end',
                    'class' => 'col-md-12',
                ]
            ]
        ]
    ];

    protected $validateRules = [
        'title' => 'required|max:30',
        'articles_id' => 'required|numeric',
        'link' => ['required','regex:/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i'],
        'url' => ['required','regex:/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i'],
        'released' => ['required', 'date_format:Y-m-d H:i:s', 'date'],
        'end' => ['required', 'date_format:Y-m-d H:i:s', 'date', 'after:released']
    ];

    protected $validateAttributes = [
        'title' => '活動名稱',
        'articles_id' => '文章ID',
        'link' => '主視覺路徑',
        'url' => 'URL',
        'released' => '活動上架時間',
        'end' => '活動下架時間',
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'numeric' => ':attribute 必須為數字',
        'regex' => '請輸入正確:attribute',
        'max' => ':attribute 最多:max 個字',
        'date_format' => ':attribute 格式不符'
    ];

    public $editField = [
        [
            'title' => '活動名稱',
            'type' => 'text',
            'name' => 'title',
            'placeholder' => '請輸入活動名稱',
            'required' => TRUE,
            'id' => 'title'
        ],
        [
            'title' => '來源',
            'type' => 'select',
            'name' => 'type_url',
            'placeholder' => '',
            'value' => 2,
            'required' => TRUE,
            'id' => 'type_url',
            'option' => [
                2 => '輸入URL',
                1 => '文章ID',
                3 => '策展內開',
                4 => '策展外開'
            ]
        ],
        [
            'title' => 'URL',
            'type' => 'text',
            'name' => 'url',
            'placeholder' => '請輸入URL',
            'required' => TRUE,
            'id' => 'url'
        ],
        [
            'title' => '文章ID',
            'type' => 'text',
            'name' => 'articles_id',
            'placeholder' => '請輸入文章ID',
            'required' => TRUE,
            'id' => 'articles_id'
        ],
        [
            'title' => '主視覺路徑',
            'type' => 'text',
            'name' => 'link',
            'placeholder' => '請輸入主視覺路徑，建議圖片尺寸：1024 * 576',
            'required' => TRUE,
            'id' => 'link',
            'class' => 'col-md-8',
            'image_gallery' => true
        ],
        [
            'title' => '活動上架時間',
            'type' => 'custome-date-start-common',
            'name' => 'released',
            'placeholder' => '請輸入活動上架時間',
            'required' => TRUE,
            'id' => 'released',
            'class' => 'col-md-6'
        ],
        [
            'title' => '活動下架時間',
            'type' => 'custome-date-end-common',
            'name' => 'end',
            'placeholder' => '請輸入活動下架時間',
            'required' => TRUE,
            'id' => 'end',
            'class' => 'col-md-6'
        ],
        [
            'title' => '活動狀態',
            'type' => 'select',
            'name' => 'status',
            'placeholder' => '',
            'value' => 0,
            'required' => TRUE,
            'id' => 'status',
            'option' => [
                0 => '下架',
                1 => '上架'
            ]
        ]
    ];

    /**
     * WebMenuController constructor.
     * @param AdminRolesService $admin_roles
     * @param AppActivitiesModel $AppActivitiesModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        AppActivitiesModel $AppActivitiesModel,
        ArticleModel $ArticleModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->AppActivitiesModel = $AppActivitiesModel;
        $this->ArticleModel = $ArticleModel;
        $this->middleware = $this->admin_roles->AdminRoles($this->role_name)->middleware;
        $this->function_role = $this->admin_roles->AdminRoles($this->role_name)->function_role;
        $this->menu_list = $this->admin_roles->AdminRoles($this->role_name)->menu_list;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role_name = $this->role_name;
        $site_name = $this->site_name;
        $page_limit = 20;
        $no_id = true;
        $search = false;
        $cond = array();
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if($keywords !== ''){
            $cond['keywords'] = $keywords;
        }

        (int)$select_type = isset($_GET['select_type']) ? $_GET['select_type'] : 1;

        if($select_type){
            $field = array('位置', '活動名稱', '活動圖', '活動上架時間', '活動下架時間', '狀態', '動作', '排序');
            $datas = $this->AppActivitiesModel
                            ->where('status', $select_type)
                            ->where('end', '>', date('Y-m-d H:i:s'))
                            ->orderBy('sort', 'ASC')
                            ->orderBy('id', 'DESC')
                            ->get();
        }else{
            $field = array('位置', '活動名稱', '活動圖', '活動上架時間', '活動下架時間');

            $cond['select_type'] = $select_type;

            $search_title = isset($_GET['search_title']) ? $_GET['search_title'] : '';
            $search_title = trim(urldecode($search_title));
            if($search_title !== ''){
                $cond['search_title'] = $search_title;
            }

            $search_start = isset($_GET['search_start']) ? date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $_GET['search_start']))) : date('Y-m-d H:i:s',strtotime('-7 day'));
            $search_start = trim(urldecode($search_start));
            if($search_start !== ''){
                $cond['search_start'] = $search_start;
            }

            $search_end = isset($_GET['search_end']) ? date('Y-m-d H:i:s', strtotime('+1 day', strtotime(str_replace("T", " ", $_GET['search_end'])))) : date('Y-m-d H:i:s',strtotime('+1 day'));
            $search_end = trim(urldecode($search_end));
            if($search_end !== ''){
                $cond['search_end'] = $search_end;
            }

            $datas = $this->AppActivitiesModel
                            ->where(function($q) use($cond){
                                if(isset($cond['search_title'])){
                                    $q->where('title', 'like', '%'.$cond['search_title'].'%');
                                }
                                if(isset($cond['search_start'])){
                                    $q->where('start', '>=', $cond['search_start']);
                                }
                                if(isset($cond['search_end'])){
                                    $q->where('start', '<', $cond['search_end']);
                                }
                            })
                            ->whereNot(function ($query) {
                                $query ->where('status', '=', 1)
                                        ->where('end', '>', date('Y-m-d H:i:s'));
                            })
                            ->orderBy('start', 'DESC')
                            ->orderBy('sort', 'ASC')
                            ->orderBy('id', 'DESC')
                            ->paginate($page_limit)
                            ->appends($cond);
        }

        $select_type = $this->select_type;
        $searchField = $this->searchField;
        $editField = $this->editField;

        return view('app.activities.index', compact('role_name','site_name','search','keywords','field','no_id','datas','select_type','searchField','editField'));
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
        $request->merge(['released' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('released'))))]);
        $request->merge(['end' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('end'))))]);

        $processedTitle = special_char_half_to_full($request->input('title'));

        $request->merge(['title' => $processedTitle]);

        if($request->input('type_url') == '1'){
            $request->merge(['url' => '']);
            unset($this->validateRules['url']);
        }else{
            $request->merge(['articles_id' => 0]);
            unset($this->validateRules['articles_id']);
        }

        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        if($request->input('type_url') == '1' && $request->input('status') == '1'){
            $errMsg = $this->judgeActivityArticle($request);
            if($errMsg){
                return response()->json(['errMsg' => $errMsg], 400);
            }
        }

        if($request->input('status') == '1' && strtotime($request->input('released')) < time()){
            $errMsg = '活動上架時間需大於當時時間';
            return response()->json(['errMsg' => $errMsg], 400);
        }

        //檢查主圖寬高是否跟所設定的寬高不同
        $errMsg = ImageHelper::judgeImageSize($request->input('link'), 1024, 576);
        if($errMsg){
            return response()->json(['errMsg' => $errMsg], 400);
        }

        $request->request->add([
            'start' => $request->input('released'),
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->AppActivitiesModel->create($request->input());

        return response()->json($request->input(), 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role_name = $this->role_name;
        $site_name = $this->site_name;

        $field = array('活動名稱', '來源', 'URL', '文章ID', '主視覺路徑', '活動上架時間', '活動下架時間', '狀態');

        $data = $this->AppActivitiesModel::where('id', $id)
                                    ->firstOrFail();
        if($data->type_url == 1){
            $field = array('活動名稱', '來源', '文章ID', '主視覺路徑', '活動上架時間', '活動下架時間', '狀態');
        }else{
            $field = array('活動名稱', '來源', 'URL', '主視覺路徑', '活動上架時間', '活動下架時間', '狀態');
        }

        return view(
            'app.activities.show',
            compact('role_name','site_name','field','data')
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result =  $this->AppActivitiesModel->find($id);
        return response()->json($result);
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
        if($id == 'save_sort'){
            $position = 1;
            $activitiesRequest = $request->input('app_activities');
            $sortId = explode(',', $request->input('sortId'));

            foreach($sortId as $v){
                $activitiesId = $activitiesRequest[$v]['id'];
                $row = $this->AppActivitiesModel->find($activitiesId);
                $activitiesUpdata = [
                    'sort' => $position,
                    'updated_user' => auth()->user()->name
                ];
                $row->update($activitiesUpdata);
                $position++;
            }
            return response()->json($request->input(), 200);
        }else{

            $request->merge(['released' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('released'))))]);
            $request->merge(['end' => date('Y-m-d H:i:s', strtotime(str_replace("T", " ", $request->input('end'))))]);

            $processedTitle = special_char_half_to_full($request->input('title'));

            $request->merge(['title' => $processedTitle]);

            if($request->input('type_url') == '1'){
                $request->merge(['url' => '']);
                unset($this->validateRules['url']);
            }else{
                $request->merge(['articles_id' => 0]);
                unset($this->validateRules['articles_id']);
            }

            $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

            if($request->input('type_url') == '1' && $request->input('status') == '1'){
                $errMsg = $this->judgeActivityArticle($request);
                if($errMsg){
                    return response()->json(['errMsg' => $errMsg], 400);
                }
            }

            // if($request->input('status') == '1' && strtotime($request->input('released')) < time()){
            //     $errMsg = '活動上架時間需大於當時時間';
            //     return response()->json(['errMsg' => $errMsg], 400);
            // }

            //檢查主圖寬高是否跟所設定的寬高不同
            $errMsg = '';
            $activity_entity = $this->AppActivitiesModel->find($id);
            if($activity_entity){
                if(strtotime(date('Y-m-d', strtotime($activity_entity['created_at']))) >= strtotime('2022-08-09')){
                    $errMsg = ImageHelper::judgeImageSize($request->input('link'), 1024, 576);
                }else{
                    $errMsg = ImageHelper::judgeImageSize($request->input('link'), 850, 478);
                }

                if($activity_entity['status'] && strtotime($activity_entity['start']) < time() && $activity_entity['start'] != $request->input('released')) {
                    $errMsg = '活動上架則不可修改上架時間';
                }
            }

            if($errMsg){
                return response()->json(['errMsg' => $errMsg], 400);
            }

            $request->request->add([
                'start' => $request->input('released'),
                'updated_user' => auth()->user()->name
            ]);

            $origin_status = $this->getStatus($activity_entity['status'], $activity_entity['start'], $activity_entity['end']);
			$new_status = $this->getStatus($request->input('status'), $request->input('start'), $request->input('end'));

			if($origin_status != $new_status) {

                // $cdn_id  = (strpos(url()->current(), '-pre') !== false) ? 'E35WXH0VBOVGI3' : 'EVPNP8E6GPPN3';

				$this->purgeCdn('/api/api_index/index');
				$this->purgeCdn('/api/api_index');
			}

            $activity_entity->update($request->input());

            return response()->json($request->input(), 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function judgeActivityArticle($request)
    {
        $errMsg = '';

        $article = $this->ArticleModel->find($request->input('articles_id'));

        if(empty($article)) {
            $errMsg = '請輸入正確 文章ID';
        }else if((bool)$article['articles_status'] == 0) {
            $errMsg = '此文章已下架，無法匯入頭條';
        }else if(strtotime($article['publish']) > strtotime($request->input('released'))) {
            $errMsg = '活動上架時間需大於文章的發布時間';
        }

        return $errMsg;
    }

    private function getStatus($status, $start, $end)
	{
		$now = time();
		$start = strtotime($start);
		$end = strtotime($end);
		return ($status == 1 && $start <= $now && $end > $now);
	}

    private function purgeCDN($item){
        try {

            $cloudFrontClient = CloudFrontClient::factory(array(
                'key'    => config('constants.aws.key_id'),
                'secret' => config('constants.aws.key_secret'),
                'version' => 'latest',
                'region' => 'us-east-1'
            ));

			$result = $cloudFrontClient->CreateInvalidation([
				'DistributionId' => config('constants.cdn.distribution_id'),
				'InvalidationBatch' => [
					'CallerReference' => time(),
					'Paths' => [
						'Items' => [$item],
						'Quantity' => 1,
					],
				]
			]);

		} catch (AwsException $e) {
            return response()->json(['AwsError' => $e->getMessage()], 400);
		}
    }
}
