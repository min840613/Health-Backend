<?php

namespace App\Observers;

use App\Models\App\AppNewsModel;

class AppNewsModelObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the AppNewsModel "created" event.
     *
     * @param  \App\Models\App\AppNewsModel  $appNewsModel
     * @return void
     */
    public function created(AppNewsModel $appNewsModel)
    {
        //
    }

    /**
     * Handle the AppNewsModel "updated" event.
     *
     * @param  \App\Models\App\AppNewsModel  $appNewsModel
     * @return void
     */
    public function updated(AppNewsModel $appNewsModel)
    {
        if (empty($appNewsModel->getChanges()) || !config('database.connections.mysql_tvbs_old.is_sync')) {
            return;
        }

        $attributes = $appNewsModel->getChanges();
        $attributes['updated_time'] = $attributes['updated_at'];
        $attributes['title'] = $attributes['message'];

        unset($attributes['updated_at'], $attributes['message']);

        \DB::connection('mysql_tvbs_old')->table('app_news')->where('site_num', 2)->update($attributes);

    }

    /**
     * Handle the AppNewsModel "deleted" event.
     *
     * @param  \App\Models\App\AppNewsModel  $appNewsModel
     * @return void
     */
    public function deleted(AppNewsModel $appNewsModel)
    {
        //
    }

    /**
     * Handle the AppNewsModel "restored" event.
     *
     * @param  \App\Models\App\AppNewsModel  $appNewsModel
     * @return void
     */
    public function restored(AppNewsModel $appNewsModel)
    {
        //
    }

    /**
     * Handle the AppNewsModel "force deleted" event.
     *
     * @param  \App\Models\App\AppNewsModel  $appNewsModel
     * @return void
     */
    public function forceDeleted(AppNewsModel $appNewsModel)
    {
        //
    }
}
