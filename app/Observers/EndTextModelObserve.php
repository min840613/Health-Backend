<?php

namespace App\Observers;

use App\Models\Articles\EndTextModel;

/**
 * Class EndTextModelObserve
 * @package App\Observers
 */
class EndTextModelObserve
{
    /**
     * @param EndTextModel $model
     */
    public function created(EndTextModel $model)
    {
        if ($model->text_type == 2 || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getAttributes();
        $attributes['order_num'] = 0;
        $attributes['update_user'] = $attributes['updated_user'];
        $attributes['ins_time'] = $attributes['created_at'];

        // 舊資料表的錯誤命名
        $attributes['updata_time'] = $attributes['updated_at'];

        // 舊的沒有 created_user
        unset($attributes['created_user'], $attributes['updated_user'], $attributes['created_at'], $attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_end_of_text')->insert($attributes);
    }

    /**
     * @param EndTextModel $model
     * isClean or isDirty 都無效
     */
    public function updated(EndTextModel $model)
    {
        if ($model->text_type == 2 || empty($model->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $model->getChanges();

        if (!empty($attributes['updated_user'])) {
            $attributes['update_user'] = $attributes['updated_user'];
        }

        $attributes['updata_time'] = $attributes['updated_at'];

        unset($attributes['updated_at'], $attributes['updated_user']);

        \DB::connection('mysql_tvbs_old')->table('health_end_of_text')->where(['text_id' => $model->text_id])->update($attributes);
    }

    /**
     * @param EndTextModel $model
     */
    public function deleted(EndTextModel $model)
    {
        if ($model->text_type == 2 || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        \DB::connection('mysql_tvbs_old')->table('health_end_of_text')->where(['text_id' => $model->text_id])->delete();
    }
}
