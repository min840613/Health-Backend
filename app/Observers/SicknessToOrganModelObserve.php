<?php

namespace App\Observers;

use App\Models\Encyclopedia\SicknessToOrganModel;

class SicknessToOrganModelObserve
{

    public function created(SicknessToOrganModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();

        \DB::connection('mysql_tvbs_old')->table('health_sickness_to_organ')->insert($attributes);
    }

    public function deleted(SicknessToOrganModel $model)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();

        \DB::connection('mysql_tvbs_old')->table('health_sickness_to_organ')->where($attributes)->delete();

    }



}
