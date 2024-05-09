<?php

namespace App\Observers;

use App\Models\Encyclopedia\OrgansModel;

class OrgansModelObserve
{

    public function created(OrgansModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();
        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];
        $attributes['icon_1'] = $attributes['icon_android'];
        $attributes['icon_2'] = $attributes['icon_ios'];

        unset($attributes['created_at'], $attributes['updated_at'], $attributes['icon_android'], $attributes['icon_ios']);

        \DB::connection('mysql_tvbs_old')->table('health_organs')->insert($attributes);
    }

    public function updated(OrgansModel $model)
    {
        if (empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];

        if (isset($attributes['icon_android']) && !empty($attributes['icon_android'])) {
            $attributes['icon_1'] = $attributes['icon_android'];
            unset($attributes['icon_android']);
        }

        if (isset($attributes['icon_ios']) && !empty($attributes['icon_ios'])) {
            $attributes['icon_2'] = $attributes['icon_ios'];
            unset($attributes['icon_ios']);
        }
        
        
        unset($attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_organs')->where(['id' => $model->id])->update($attributes);
    }
}
