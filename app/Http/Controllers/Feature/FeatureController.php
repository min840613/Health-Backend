<?php

namespace App\Http\Controllers\Feature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feature;
use Carbon\Carbon;
use App\Services\AdminRolesService;

class FeatureController extends Controller
{
    private $role_name = 'feature_manage';
    private $admin_roles;

    function __construct(AdminRolesService $admin_roles)
    {
        $this->admin_roles = $admin_roles;
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
        $page_limit = 10;
        $features = Feature::orderBy('id', 'DESC')->paginate($page_limit);
        return view('feature.feature_manage.index', compact('features'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $features = Feature::pluck('title','title')->all();
        return view('feature.feature_manage.create',compact('features'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'      => 'required',
            'feature'     => 'required|unique:features|max:64'
        ]);

        $input = $request->all();
        if (isset($input['active_at'])) {
            $input['active_at'] = Carbon::now();
        } else {
            $input['active_at'] = null;
        }
        $data = Feature::create($input);

        // return redirect('admin/feature/feature_manage')
        return redirect()->route('feature_manage.index')
                ->with('success','Feature created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $feature = Feature::find($id);
        return view('feature.feature_manage.show',compact('feature'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $feature = Feature::find($id);
        return view('feature.feature_manage.edit',compact('feature'));
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
        $this->validate($request, [
            'title'      => 'required',
            'feature'     => 'required|max:64'
        ]);

        $input = $request->all();

        if (isset($input['active_at'])) {
            $input['active_at'] = Carbon::now();
        } else {
            $input['active_at'] = null;
        }

        $feature = Feature::find($id);
        $feature->update($input);

        return redirect()->route('feature_manage.index')
                        ->with('success','Feature updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Feature::find($id)->delete();
        return redirect()->route('feature_manage.index')
                ->with('success','Feature deleted successfully');
    }
}
