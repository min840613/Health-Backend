<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\role_has_permissions;
use DB;


class EditPermissionForUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'EditPermissionForUser:Run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '賦予管理者（Admin）新功能權限';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $config_menu = config('adminlte.menu');

        Permission::where('status',true)
                    ->update(['status'=>false]);

        $function_role = [
           'list',
           'create',
           'edit',
           'delete'
        ];
        $permissions = array();
        foreach($config_menu as $k=>$v):
            if($k !== 0):
                foreach($function_role as $kkk=>$vvv):
                    $permissions[] = array(
                        'level'     =>  1,
                        'name'      =>  $v['role_name'].'-'.$vvv,
                        'guard_name'=>  'web',
                        'status'    =>  true
                    );
                    foreach($v['submenu'] as $kk=>$vv):
                        $permissions[] = array(
                            'level'     =>  2,
                            'name'      =>  $vv['role_name'].'-'.$vvv,
                            'guard_name'=>  'web',
                            'status'    =>  true
                        );
                        if(isset($vv['submenu']) && $vv['submenu']):
                            foreach($vv['submenu'] as $kkkk=>$vvvv):
                                $permissions[] = array(
                                    'level'     =>  3,
                                    'name'      =>  $vvvv['role_name'].'-'.$vvv,
                                    'guard_name'=>  'web',
                                    'status'    =>  true
                                );
                            endforeach;
                        endif;
                    endforeach;
                endforeach;
            endif;
        endforeach;
        foreach($permissions as $permission):
            $data = Permission::get()
                    ->where('name',$permission['name'])
                    ->first();
            if($data):
                $update_data = Permission::find($data['id']);
                $update_data->status = true;
                $update_data->save();
            else:
                Permission::create(
                    [
                        'level'     =>  $permission['level'],
                        'name'      =>  $permission['name'],
                        'guard_name'=>  $permission['guard_name'],
                        'status'    =>  true,
                    ]
                );
            endif;
        endforeach;
        $role = Role::where(['name' => 'Admin'])
                    ->first();
        DB::table('role_has_permissions')
                ->where('role_id',$role->id)
                ->delete();
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);
    }
}
