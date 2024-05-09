<?php

namespace App\Observers;

use App\Models\Encyclopedia\SicknessModel;

class SicknessModelObserve
{

    public function created(SicknessModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();
        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['created_at'], $attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_sickness')->insert($attributes);



    }

    public function updated(SicknessModel $model)
    {
        if (empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_sickness')->where(['id' => $model->id])->update($attributes);
    }
}
