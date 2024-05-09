<?php

namespace App\Observers;

use App\Models\App\ActivitiesAnnouncementModel;

/**
 * Class ActivitiesAnnouncementModelObserve
 * @package App\Observers
 */
class ActivitiesAnnouncementModelObserve
{
    /**
     * @param ActivitiesAnnouncementModel $model
     */
    public function created(ActivitiesAnnouncementModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();
        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['created_at'], $attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_activities_announcement')->insert($attributes);
    }

    /**
     * @param ActivitiesAnnouncementModel $model
     * isClean or isDirty 都無效
     */
    public function updated(ActivitiesAnnouncementModel $model)
    {
        if (empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_activities_announcement')->where(['id' => $model->id])->update($attributes);
    }
}
