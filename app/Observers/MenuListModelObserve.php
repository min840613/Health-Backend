<?php

namespace App\Observers;

use App\Models\Categories\MenuListModel;

/**
 * Class MenuListModelObserve
 * @package App\Observers
 */
class MenuListModelObserve
{
    /**
     * @param MenuListModel $model
     */
    public function created(MenuListModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();
        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['created_at'], $attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_menu_list')->insert($attributes);
    }

    /**
     * @param MenuListModel $model
     * isClean or isDirty 都無效
     */
    public function updated(MenuListModel $model)
    {
        if (empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_menu_list')->where(['menu_list_id' => $model->menu_list_id])->update($attributes);
    }
}
