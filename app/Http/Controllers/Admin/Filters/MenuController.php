<?php

namespace App\Http\Controllers\Admin\Filters;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class MenuController extends Authenticatable
{
    /**
     * Transforms a menu item. Adds the compiled data attributes when suitable.
     *
     * @param  array  $item  A menu item
     * @return array The transformed menu item
     */
    public function transform($item)
    {
        if (isset($item['role_name'])) {
            $item['can'] = $this->compileData($item);
        }
        return $item;
    }

    /**
     * Compile an array of data attributes into a data string.
     *
     * @param  array  $dataArray  Array of html data attributes
     * @return string The compiled version of data attributes
     */
    protected function compileData($dataArray)
    {
        $permission_list = array('list','create','edit','delete');
        $result_array = array();
        foreach($permission_list as $value):
            $result_array[] = $dataArray['role_name'].'-'.$value;
        endforeach;
        return $result_array;
    }
}
