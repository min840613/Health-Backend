<?php

namespace App\Observers;

use App\Models\App\AppSplashModel;

class AppSplashModelObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the AppSplashModel "created" event.
     *
     * @param  \App\Models\App\AppSplashModel  $appSplashModel
     * @return void
     */
    public function created(AppSplashModel $appSplashModel)
    {
        if (!config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $appSplashModel->getAttributes();

        $attributes['created_time'] = $attributes['created_at'];
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['created_at'], $attributes['updated_at'], $attributes['id']);

        \DB::connection('mysql_tvbs_old')->table('health_app_splash')->insert($attributes);
    }

    /**
     * Handle the AppSplashModel "updated" event.
     *
     * @param  \App\Models\App\AppSplashModel  $appSplashModel
     * @return void
     */
    public function updated(AppSplashModel $appSplashModel)
    {
        if (empty($appSplashModel->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $appSplashModel->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];

        unset($attributes['updated_at']);

        \DB::connection('mysql_tvbs_old')->table('health_app_splash')->where(['splash_id' => $appSplashModel->id])->update($attributes);
    }

    /**
     * Handle the AppSplashModel "deleted" event.
     *
     * @param  \App\Models\App\AppSplashModel  $appSplashModel
     * @return void
     */
    public function deleted(AppSplashModel $appSplashModel)
    {
        //
    }

    /**
     * Handle the AppSplashModel "restored" event.
     *
     * @param  \App\Models\App\AppSplashModel  $appSplashModel
     * @return void
     */
    public function restored(AppSplashModel $appSplashModel)
    {
        //
    }

    /**
     * Handle the AppSplashModel "force deleted" event.
     *
     * @param  \App\Models\App\AppSplashModel  $appSplashModel
     * @return void
     */
    public function forceDeleted(AppSplashModel $appSplashModel)
    {
        //
    }
}
