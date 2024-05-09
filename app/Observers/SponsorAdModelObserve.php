<?php

namespace App\Observers;

use App\Enums\SponsorAdCategoriesType;
use App\Models\Articles\SponsorAdModel;

/**
 * Class SponsorAdModelObserve
 * @package App\Observers
 */
class SponsorAdModelObserve
{
    /**
     * @param SponsorAdModel $model
     */
    public function created(SponsorAdModel $model)
    {
        if ($model->categories_type !== SponsorAdCategoriesType::MAIN || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();
        $attributes['start_time'] = $attributes['start'];
        $attributes['end_time'] = $attributes['end'];
        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['created_at'], $attributes['updated_at'], $attributes['start'], $attributes['end'], $attributes['categories_type']);

        \DB::connection('mysql_tvbs_old')->table('health_sponsor_ad')->insert($attributes);
    }

    /**
     * @param SponsorAdModel $model
     * isClean or isDirty 都無效
     */
    public function updated(SponsorAdModel $model)
    {
        if ($model->categories_type !== SponsorAdCategoriesType::MAIN || empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();

        if (!empty($attributes['start'])) {
            $attributes['start_time'] = $attributes['start'];
            unset($attributes['start']);
        }
        if (!empty($attributes['end'])) {
            $attributes['end_time'] = $attributes['end'];
            unset($attributes['end']);
        }
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['updated_at'], $attributes['categories_type']);

        \DB::connection('mysql_tvbs_old')->table('health_sponsor_ad')->where(['id' => $model->id])->update($attributes);
    }
}
