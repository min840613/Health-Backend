<?php

namespace App\Observers;

use App\Models\App\SystemAnnouncementModel;

/**
 * Class SystemAnnouncementModelObserve
 * @package App\Observers
 */
class SystemAnnouncementModelObserve
{
    /**
     * @param SystemAnnouncementModel $model
     */
    public function created(SystemAnnouncementModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();
        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['created_at'], $attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_system_announcement')->insert($attributes);
    }

    /**
     * @param SystemAnnouncementModel $model
     * isClean or isDirty 都無效
     */
    public function updated(SystemAnnouncementModel $model)
    {
        if (empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_system_announcement')->where(['id' => $model->id])->update($attributes);
    }
}
