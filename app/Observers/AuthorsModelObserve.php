<?php

namespace App\Observers;

use App\Models\Authors\AuthorsModel;

/**
 * Class AuthorsModelObserve
 * @package App\Observers
 */
class AuthorsModelObserve
{
    /**
     * @param AuthorsModel $model
     */
    public function created(AuthorsModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();
        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['created_at'], $attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_authors')->insert($attributes);
    }

    /**
     * @param AuthorsModel $model
     * isClean or isDirty 都無效
     */
    public function updated(AuthorsModel $model)
    {
        if (empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_authors')->where(['id' => $model->id])->update($attributes);
    }
}
