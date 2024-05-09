<?php

namespace App\Observers;

use App\Models\App\AppVersionsModel;

class AppVersionsModelObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the AppVersionsModel "created" event.
     *
     * @param  \App\Models\App\AppVersionsModel  $appVersionsModel
     * @return void
     */
    public function created(AppVersionsModel $appVersionsModel)
    {
        //
    }

    /**
     * Handle the AppVersionsModel "updated" event.
     *
     * @param  \App\Models\App\AppVersionsModel  $appVersionsModel
     * @return void
     */
    public function updated(AppVersionsModel $appVersionsModel)
    {
        if (empty($appVersionsModel->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $appVersionsModel->getChanges();
        $attributes['update'] = $attributes['updated_at'];

        unset($attributes['updated_at'], $attributes['updated_user']);

        \DB::connection('mysql_tvbs_old')->table('health_app_version')->where('id', $appVersionsModel->id)->update($attributes);

    }

    /**
     * Handle the AppVersionsModel "deleted" event.
     *
     * @param  \App\Models\App\AppVersionsModel  $appVersionsModel
     * @return void
     */
    public function deleted(AppVersionsModel $appVersionsModel)
    {
        //
    }

    /**
     * Handle the AppVersionsModel "restored" event.
     *
     * @param  \App\Models\App\AppVersionsModel  $appVersionsModel
     * @return void
     */
    public function restored(AppVersionsModel $appVersionsModel)
    {
        //
    }

    /**
     * Handle the AppVersionsModel "force deleted" event.
     *
     * @param  \App\Models\App\AppVersionsModel  $appVersionsModel
     * @return void
     */
    public function forceDeleted(AppVersionsModel $appVersionsModel)
    {
        //
    }
}
