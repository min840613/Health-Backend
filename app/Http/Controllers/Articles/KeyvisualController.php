<?php

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\Articles\KeyvisualModel;
use App\Models\Articles\ArticleModel;
use App\Helpers\UrlHelper;
use Session;

class KeyvisualController extends Controller
{
    protected KeyvisualModel $KeyvisualModel;
    protected ArticleModel $ArticleModel;

    private $role_name = 'keyvisual';
    private $site_name = 'keyvisual';

    protected $validateRules = [
        'title' => 'required',
        'link'  => 'required',
        'image' => 'required',
        'start' => 'required',
        'end'   => 'required|after:start',
    ];

    protected $validateAttributes = [
        'title' => '文章標題',
        'link'  => '頭條URL',
        'image' => '首頁主圖路徑',
        'start' => '發佈時間',
        'end'   => '下架時間',
    ];

    /**
     * WebMenuController constructor.
     * @param AdminRolesService $admin_roles
     * @param MenuListModel $MenuListModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        UrlHelper $UrlHelper,
        KeyvisualModel $KeyvisualModel,
        ArticleModel $ArticleModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->KeyvisualModel = $KeyvisualModel;
        $this->ArticleModel = $ArticleModel;
        $this->UrlHelper = $UrlHelper;
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
        // $page_limit = 20;
        $no_id = true;
        $search = false;
        $cond = array();
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if($keywords !== ''){
            $cond['keywords'] = $keywords;
        }
        $field = array('位置', '發佈時間', '下架時間', '標題', '圖片', '動作', '排序');

        $datas = $this->KeyvisualModel::with('article')
                                    ->orderBy('status', 'DESC')
                                    ->orderBy('sort', 'DESC')
                                    ->orderBy('keyvisual_id','DESC')
                                    ->get();
        $isShowCount = 1;
        foreach($datas as $k=>$v):
            $image = $this->UrlHelper->parseUrl($v['image']);
            $datas[$k]['image'] = str_replace('https://tvbs-media.s3.ap-northeast-1.amazonaws.com/',env('AWS_S3_URL'),$image);
            if($v['status'] == 1 && strtotime($v['start']) <= time() && strtotime($v['end']) >= time() && $isShowCount <= 6):
                if($v['article']):
                    if(strtotime($v['article']['publish']) <= time()):
                        $datas[$k]['isShow'] = true;
                        $isShowCount++;
                    else:
                        $datas[$k]['isShow'] = false;
                    endif;
                else:
                    $datas[$k]['isShow'] = true;
                    $isShowCount++;
                endif;
            else:
                $datas[$k]['isShow'] = false;
            endif;
        endforeach;
        
        $editField = [];
        return view(
            'articles.keyvisual.index',
            compact('role_name','site_name','search','keywords','field','no_id','datas','editField')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $role_name = $this->role_name;
        $site_name = $this->site_name;

        $request_data = $request->all();
        if(isset($request_data['article_id']) && $request_data['article_id']):

            if($this->KeyvisualModel->where('source_id', $request_data['article_id'])->first()):
                return redirect()->route('keyvisual.index',array('article_id'=>$request_data['article_id']))->with('error','該文章已存在於頭條管理');
            endif;

            $article_data = $this->ArticleModel::where('articles_status', 1)
                                        ->where('articles_id', $request_data['article_id'])
                                        ->first();
            if($article_data):
                $data = array(
                    'source_id'    =>  $article_data['articles_id'],
                    'title' =>  $article_data['title'],
                    'link'  =>  config('constants.frontend_url').'/'.$article_data->mainCategories[0]->en_name.'/'.$article_data['articles_id'],
                    'image' =>  str_replace('https://tvbs-media.s3.ap-northeast-1.amazonaws.com/',env('AWS_S3_URL'),$this->UrlHelper->parseUrl($article_data['image'])),
                    'start' =>  date('Y-m-d H:i:s'),
                    'end'   =>  date('Y-m-d H:i:s',strtotime("+7 day"))
                );

            else:
                $data = [];
            endif;
        else:
            $data = [];
        endif;

        $field = array();
        foreach($this->validateAttributes as $k=>$v):
            $Attributesdata = array();
            $Attributesdata['title'] = $v;
            if(isset($this->validateRules[$k]) && strpos($this->validateRules[$k],'required') !== false):
                $Attributesdata['is_required'] = true;
            else:
                $Attributesdata['is_required'] = false;
            endif;
            $field[] = $Attributesdata;
        endforeach;

        $editField = [];

        return view(
            'articles.keyvisual.create',
            compact('role_name','site_name','field', 'data', 'editField')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->validateRules, [], $this->validateAttributes);

        // 判斷文章ID是否已經存在
        $source_id = $request->input('source_id');
        if($source_id):
            if($this->KeyvisualModel->where('source_id', $request->input('source_id'))->first()):
                return redirect()->route('keyvisual.create',array('article_id'=>$source_id))
                                ->with('error','該文章已存在於頭條管理');
                // return response()->json(['errMsg' => '該文章已存在於頭條管理'], 400);
            endif;
            $article_data = $this->ArticleModel::where('articles_status', 1)
                                        ->where('articles_id', $source_id)
                                        ->first();
            if(!$article_data):
                return redirect()->route('keyvisual.create',array('article_id'=>$source_id))
                                ->with('error','該文章不存在');
            else:
                $article_data['categories'] = $article_data->MainCategories[0];
            endif;
        else:
            $article_data = [];
        endif;

        $processedName = special_char_half_to_full($request->input('title'));

        $request->merge(['title' => $processedName]);

        $request->request->add([
            'sort' =>  '99',
            'type' => ($article_data?$article_data['categories']['en_name']:NULL),
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);
        $result = $this->KeyvisualModel->create($request->input());

        $ArticleListData = $this->KeyvisualModel::orderBy('status', 'DESC')
                                    ->orderBy('sort', 'DESC')
                                    ->orderBy('keyvisual_id','DESC')
                                    ->limit(8)
                                    ->get();
        $ids = array();
        foreach ($ArticleListData as $item => $data) {
            $ids[$item] = $data['keyvisual_id'];
        }
        $this->KeyvisualModel::whereNotIn('keyvisual_id',$ids)->delete();
        
        return redirect()->route('keyvisual.index')
                ->with('success','儲存成功');
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

        $field = array();
        foreach($this->validateAttributes as $k=>$v):
            $Attributesdata = array();
            $Attributesdata['title'] = $v;
            if(isset($this->validateRules[$k]) && strpos($this->validateRules[$k],'required') !== false):
                $Attributesdata['is_required'] = true;
            else:
                $Attributesdata['is_required'] = false;
            endif;
            $field[] = $Attributesdata;
        endforeach;

        $data = $this->KeyvisualModel::where('status', 1)
                                    ->where('keyvisual_id', $id)
                                    ->firstOrFail();
        $image = $this->UrlHelper->parseUrl($data['image']);
        $data['image'] = str_replace('https://tvbs-media.s3.ap-northeast-1.amazonaws.com/',env('AWS_S3_URL'),$image);

        return view(
            'articles.keyvisual.show',
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
        $role_name = $this->role_name;
        $site_name = $this->site_name;

        if($id):
            $ArticleData = $this->KeyvisualModel::where('keyvisual_id', $id)->First();
            if(!$ArticleData):
                return redirect()->route('keyvisual.index')
                                ->with('error','該文章不存在');
            else:
                $data = $ArticleData;
            endif;
        else:
            return redirect()->route('keyvisual.index')
                                ->with('error','該文章不存在');
        endif;

        $field = array();
        foreach($this->validateAttributes as $k=>$v):
            $Attributesdata = array();
            $Attributesdata['title'] = $v;
            if(isset($this->validateRules[$k]) && strpos($this->validateRules[$k],'required') !== false):
                $Attributesdata['is_required'] = true;
            else:
                $Attributesdata['is_required'] = false;
            endif;
            $field[] = $Attributesdata;
        endforeach;
        
        return view(
            'articles.keyvisual.edit',
            compact('role_name','site_name','field', 'data')
        );
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
        if($id != 'all'):
            $this->validate($request, $this->validateRules, [], $this->validateAttributes);
            $row = $this->KeyvisualModel->find($id);
            $request->request->add([
                'updated_user' => auth()->user()->name
            ]);
            $row->update($request->input());

            return redirect()->route('keyvisual.index')
                    ->with('success','儲存成功');
        else:
            $errMsg = '';
            
            $position = 1;
            $sortRequest = $request->input('data');

            $sortRequest = array_reverse($sortRequest);
            foreach($sortRequest as $k=>$v){
                $sortListId = $sortRequest[$k]['keyvisual_id'];
                $row = $this->KeyvisualModel->find($sortListId);
                $sortUpdata = [
                    'sort' => $position,
                    'updated_user' => auth()->user()->name
                ];
                $row->update($sortUpdata);
                $position++;
            }
            return response()->json($request->input('data'), 200);
        endif;
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
}
