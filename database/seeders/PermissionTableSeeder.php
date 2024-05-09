<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $config_menu = config('adminlte.menu');

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

        foreach ($permissions as $permission) {
            Permission::create(
                [
                    'level'     =>  $permission['level'],
                    'name'      =>  $permission['name'],
                    'guard_name'=>  $permission['guard_name'],
                    'status'    =>  $permission['status'],
                ]
            );
        }
    }
}
