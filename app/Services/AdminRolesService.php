<?php

namespace App\Services;

use App\Http\Controllers\Controller;

class AdminRolesService extends Controller
{
    public function AdminRoles($role_name){
        $config_menu = config('adminlte.menu');
        $this->function_role = [
           'list',
           'create',
           'edit',
           'delete'
        ];
        $this->menu_list = array();
        foreach($config_menu as $k=>$v):
            if(isset($v['submenu']) && $v['submenu']):
                $is_have = false;
                foreach($v['submenu'] as $vv):
                    if($vv['role_name'] == $role_name):
                        $this->menu_list[] = $vv;
                        $is_have = true;
                    endif;
                endforeach;
                if(!$is_have):
                    foreach($v['submenu'] as $vv):
                        if(isset($vv['submenu']) && $vv['submenu']):
                            foreach($vv['submenu'] as $vvv):
                                if($vvv['role_name'] == $role_name):
                                    $this->menu_list[] = $vvv;
                                endif;
                            endforeach;
                        endif;
                    endforeach;
                endif;
            endif;
        endforeach;
        if(!$this->menu_list):
            abort(404);
        endif;

        $index_str = array();
        foreach($this->menu_list as $k=>$v):
            foreach($this->function_role as $vv):
                if($index_str):
                    $index_str = $index_str.'|'.$v['role_name'].'-'.$vv;
                else:
                    $index_str = $v['role_name'].'-'.$vv;
                endif;
            endforeach;
        endforeach;
        $this->middleware('permission:'.$index_str, ['only' => ['index','store']]);

        $create_str = array();
        foreach($this->menu_list as $k=>$v):
            foreach($this->function_role as $vv):
                if(in_array($vv,array('create'))):
                    if($create_str):
                        $create_str = $create_str.'|'.$v['role_name'].'-'.$vv;
                    else:
                        $create_str = $v['role_name'].'-'.$vv;
                    endif;
                endif;
            endforeach;
        endforeach;
        $this->middleware('permission:'.$create_str, ['only' => ['create','store']]);

        $edit_str = array();
        foreach($this->menu_list as $k=>$v):
            foreach($this->function_role as $vv):
                if(in_array($vv,array('edit'))):
                    if($edit_str):
                        $edit_str = $edit_str.'|'.$v['role_name'].'-'.$vv;
                    else:
                        $edit_str = $v['role_name'].'-'.$vv;
                    endif;
                endif;
            endforeach;
        endforeach;
        $this->middleware('permission:'.$edit_str, ['only' => ['edit','update']]);

        $destroy_str = array();
        foreach($this->menu_list as $k=>$v):
            foreach($this->function_role as $vv):
                if(in_array($vv,array('edit'))):
                    if($destroy_str):
                        $destroy_str = $destroy_str.'|'.$v['role_name'].'-'.$vv;
                    else:
                        $destroy_str = $v['role_name'].'-'.$vv;
                    endif;
                endif;
            endforeach;
        endforeach;
        $this->middleware('permission:'.$destroy_str, ['only' => ['destroy']]);
        
        return $this;
    }
}