<?php

namespace App\Observers;

use App\Models\App\AppActivitiesModel;

/**
 * Class AppActivitiesModelObserve
 * @package App\Observers
 */
class AppActivitiesModelObserve
{
    /**
     * @param AppActivitiesModel $model
     */
    public function created(AppActivitiesModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();
        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['created_at'], $attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_app_activities')->insert($attributes);
    }

    /**
     * @param AppActivitiesModel $model
     * isClean or isDirty 都無效
     */
    public function updated(AppActivitiesModel $model)
    {
        if (empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_app_activities')->where(['id' => $model->id])->update($attributes);
    }
}
