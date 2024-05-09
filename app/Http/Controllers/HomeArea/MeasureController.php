<?php

namespace App\Http\Controllers\HomeArea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;

use App\Models\HomeArea\MeasureModel;

class MeasureController extends Controller
{
    protected MeasureModel $MeasureModel;

    private $role_name = 'measure';
    private $site_name = 'measure';

    public $admin_roles;

    protected $validateRules = [
        'title' => 'required|between:1,6',
        'link'  => 'required',
        'image' => 'required',
        'start' => 'required',
        'end'   => 'required|after:start'
    ];

    protected $validateAttributes = [
        'title' => '量測名稱',
        'link'  => '量測URL',
        'image' => '主圖URL',
        'start' => '上架時間',
        'end'   => '下架時間',
        'status'=> '狀態'
    ];

    /**
     * WebMenuController constructor.
     * @param AdminRolesService $admin_roles
     * @param MenuListModel $MenuListModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        MeasureModel $MeasureModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->MeasureModel = $MeasureModel;
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
        $no_id = true;
        $search = false;
        $cond = array();
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        if($keywords !== ''){
            $cond['keywords'] = $keywords;
        }
        $field = array('ID', '量測名稱', '圖片', '上架時間', '下架時間', '狀態', '動作', '排序');

        $datas = $this->MeasureModel::orderBy('status', 'DESC')
                                    ->orderBy('sort', 'DESC')
                                    ->orderBy('id','DESC')
                                    ->get();
        $i = 4;
        foreach($datas as $k=>$v):
            if(strtotime($v['start']) <= time() && strtotime($v['end']) >= time() && $v['status'] == 1 && $i > 0):
                $datas[$k]['isShow'] = true;
            else:
                $datas[$k]['isShow'] = false;
            endif;
            $i--;
        endforeach;
        $editField = [];
        return view(
            'homearea.measure.index',
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
            'homearea.measure.create',
            compact('role_name','site_name','field', 'editField')
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

        $processedName = special_char_half_to_full($request->input('title'));
        $request->merge([
            'title'     =>  $processedName
        ]);

        $request->request->add([
            'sort' =>  '99',
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);
        $result = $this->MeasureModel->create($request->input());

        return redirect()->route('measure.index')
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

        $data = $this->MeasureModel::where('id', $id)
                                    ->firstOrFail();
        return view(
            'homearea.measure.show',
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
            $ArticleData = $this->MeasureModel::where('id', $id)->First();
            if(!$ArticleData):
                return redirect()->route('measure.index')
                                ->with('error','該文章不存在');
            else:
                $data = $ArticleData;
            endif;
        else:
            return redirect()->route('measure.index')
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
            'homearea.measure.edit',
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
            $row = $this->MeasureModel->find($id);

            $request->merge([
                'start'        => date('Y-m-d H:i:s',strtotime($request['start'])),
                'end'          => date('Y-m-d H:i:s',strtotime($request['end']))
            ]);
            $request->request->add([
                'updated_user' => auth()->user()->name
            ]);
            $row->update($request->input());
            return redirect()->route('measure.index')
                    ->with('success','儲存成功');
        else:
            $errMsg = '';
            
            $position = 1;
            $sortRequest = $request->input('data');

            $sortRequest = array_reverse($sortRequest);
            foreach($sortRequest as $k=>$v){
                $sortListId = $sortRequest[$k]['id'];
                $row = $this->MeasureModel->find($sortListId);
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