<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminRolesService;
use App\Models\App\AppVersionsModel;
use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use Session;

class AppVersionsController extends Controller
{
    protected AppActivitiesModel $AppActivitiesModel;
    private $role_name = 'app_versions';
    private $site_name = 'app_versions';

    protected $validateRules = [
        'device' => 'max:30',
        'version' => 'required',
        'limit_version' => 'required',
        'release_note' => 'required'
    ];

    protected $validateAttributes = [
        'device' => '裝置',
        'version' => '版本',
        'limit_version' => '最低版本',
        'release_note' => '更新資訊'
    ];

    protected $validateMessages = [
        'required' => ':attribute 為必填',
        'max' => ':attribute 最多:max 個字'
    ];

    public $editField = [
        [
            'title' => '裝置',
            'type' => 'text',
            'name' => 'device',
            'placeholder' => '請輸入裝置名稱',
            'required' => TRUE,
            'id' => 'device'
        ],
        [
            'title' => '版本',
            'type' => 'text',
            'name' => 'version',
            'placeholder' => '請輸入版本',
            'required' => TRUE,
            'id' => 'version'
        ],
        [
            'title' => '最低版本',
            'type' => 'text',
            'name' => 'limit_version',
            'placeholder' => '請輸入最低版本',
            'required' => TRUE,
            'id' => 'limit_version'
        ],
        [
            'title' => '更新資訊',
            'type' => 'textarea',
            'name' => 'release_note',
            'placeholder' => '請輸入更新資訊',
            'required' => TRUE,
            'id' => 'release_note'
        ]
    ];

    /**
     * WebMenuController constructor.
     * @param AdminRolesService $admin_roles
     * @param AppVersionsModel $AppVersionsModel
     */
    public function __construct(
        AdminRolesService $admin_roles,
        AppVersionsModel $AppVersionsModel
    )
    {
        $this->admin_roles = $admin_roles;
        $this->AppVersionsModel = $AppVersionsModel;
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
        $field = array('裝置', '版本', '最低版本', '更新資訊', '動作');
        $datas = $this->AppVersionsModel::paginate($page_limit);
        $editField = $this->editField;

        return view('app.versions.index', compact('role_name', 'site_name', 'field', 'datas', 'editField'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);


        $request->request->add([
            'created_user' => auth()->user()->name,
            'updated_user' => auth()->user()->name
        ]);

        $this->AppVersionsModel->create($request->input());

        return response()->json($request->input(), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result =  $this->AppVersionsModel->find($id);
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
        $this->validate($request, $this->validateRules, $this->validateMessages, $this->validateAttributes);

        $row = $this->AppVersionsModel->find($id);

        $request->request->add([
            'updated_user' => auth()->user()->name
        ]);

        $row->update($request->input());

        if ($request->input()['id'] == 1) {
            $this->purgeCDN('/api/api_index/version/i*');
        } else {
            $this->purgeCDN('/api/api_index/version/a*');
        }

        return response()->json($request->input(), 200);
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
