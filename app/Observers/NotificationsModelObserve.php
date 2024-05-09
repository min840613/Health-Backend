<?php

namespace App\Observers;

use App\Models\App\NotificationsModel;

/**
 * Class NotificationsModelObserve
 * @package App\Observers
 */
class NotificationsModelObserve
{
    /**
     * @param NotificationsModel $model
     */
    public function created(NotificationsModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }
        $attributes = $model->getAttributes();
        $attributes['push_notifications_id'] = $attributes['id'];
        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_push_notifications')->insert($attributes);
    }

    /**
     * @param NotificationsModel $model
     */
    public function updated(NotificationsModel $model)
    {
        if (empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_push_notifications')->where(['push_notifications_id' => $model->id])->update($attributes);
    }
}
