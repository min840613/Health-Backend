<?php

namespace App\Observers;

use App\Models\Categories\MainCategoriesModel;

/**
 * Class MainCategoriesModelObserve
 * @package App\Observers
 */
class MainCategoriesModelObserve
{
    /**
     * @param MainCategoriesModel $model
     */
    public function created(MainCategoriesModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();
        $attributes['publish'] = now();
        $attributes['show_category_menu'] = '0';
        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['created_at'], $attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_categories')->insert($attributes);
    }

    /**
     * @param MainCategoriesModel $model
     * isClean or isDirty 都無效
     */
    public function updated(MainCategoriesModel $model)
    {
        if (empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_categories')->where(['categories_id' => $model->categories_id])->update($attributes);
    }
}
