<?php

namespace App\Observers;

use App\Models\App\ShakeModel;

/**
 * Class ShakeModelObserve
 * @package App\Observers
 */
class ShakeModelObserve
{
    /**
     * @param ShakeModel $model
     */
    public function created(ShakeModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }
        $attributes = $model->getAttributes();
        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['created_at'], $attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_shake')->insert($attributes);
    }

    /**
     * @param ShakeModel $model
     */
    public function updated(ShakeModel $model)
    {
        if (empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_shake')->where(['shake_id' => $model->shake_id])->update($attributes);
    }
}
