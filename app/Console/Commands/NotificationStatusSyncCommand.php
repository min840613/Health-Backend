<?php

namespace App\Console\Commands;

use App\Enums\NotificationsStatus;
use App\Models\App\NotificationsModel;
use Illuminate\Console\Command;

class NotificationStatusSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification_status:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步推播管理的狀態 (30分鐘同步一次)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $selfNotifications = NotificationsModel::query()
            ->where('push_notifications_status', NotificationsStatus::PENDING)
            ->where('prepush', '<=', now())
            ->get();

        if ($selfNotifications->isEmpty()) {
            return;
        }

        $notifications = NotificationsModel::on('mysql_tvbs_old')
            ->whereIn('push_notifications_id', $selfNotifications->pluck('id')->toArray())
            ->whereNot('push_notifications_status', NotificationsStatus::PENDING)
            ->get();

        \DB::transaction(function () use ($notifications) {
            $notifications->each(function (NotificationsModel $notification) {
                $this->updateWithoutEvents($notification);
            });
        });
    }

    /**
     * @param NotificationsModel $notification
     */
    public function updateWithoutEvents(NotificationsModel $notification)
    {
        NotificationsModel::withoutEvents(function () use ($notification) {
            NotificationsModel::find($notification->push_notifications_id)->update([
                'pushed' => $notification->pushed,
                'push_notifications_status' => $notification->push_notifications_status,
            ]);
        });
    }
}
