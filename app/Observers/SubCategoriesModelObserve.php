<?php

namespace App\Observers;

use App\Models\Categories\SubCategoriesModel;

/**
 * Class SubCategoriesModelObserve
 * @package App\Observers
 */
class SubCategoriesModelObserve
{
    /**
     * @param SubCategoriesModel $model
     */
    public function created(SubCategoriesModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();

        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['en_name'], $attributes['meta_title'], $attributes['description'], $attributes['created_at'], $attributes['updated_at'], $attributes['status'], $attributes['sort']);

        \DB::connection('mysql_tvbs_old')->table('health_sub_categories')->insert($attributes);
    }

    /**
     * @param SubCategoriesModel $model
     * isClean or isDirty 都無效
     */
    public function updated(SubCategoriesModel $model)
    {
        if (empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['en_name'], $attributes['meta_title'], $attributes['description'], $attributes['updated_at'], $attributes['status'], $attributes['sort']);

        \DB::connection('mysql_tvbs_old')->table('health_sub_categories')->where(['sub_categories_id' => $model->sub_categories_id])->update($attributes);
    }
}
