<?php

namespace App\Console\Commands;

use App\Enums\NotificationsStatus;
use App\Enums\NotificationsType;
use App\Helpers\NotifyHelper;
use App\Models\App\NotificationsModel;
use Illuminate\Console\Command;

/**
 * Class AppNotificationsCommand
 * @package App\Console\Commands
 */
class AppNotificationsCommand extends Command
{
    /** @var array */
    private array $allowTypes = [
        NotificationsType::ARTICLE,
        NotificationsType::SYSTEM,
        NotificationsType::ACTIVITY,
        NotificationsType::SHAKE,
    ];

    /** @var string */
    private string $topicName = 'health_test';

    /** @var int */
    public const LIMIT = 10;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'APP 推播通知';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->environment(['production'])) {
            $this->topicName = 'health';
        }

        $now = now()->toDateTimeString();

        $notifications = NotificationsModel::where('push_notifications_status', 1)
            ->whereNull('pushed')
            ->where('prepush', '<=', $now)
            ->whereIn('type', $this->allowTypes)
            ->limit(self::LIMIT)
            ->get();

        $notifications->each(function (NotificationsModel $notification) use ($now) {
            $pushData = $this->factory($notification);

            if (empty($pushData['announcement'])) {
                \Log::alert('push notifications error.', collect($notification)->toArray());

                $notification->update(['push_notifications_status' => NotificationsStatus::FAILED]);
                return;
            }

            $messageJSON = $this->encode([
                'default' => $notification->message,
                'APNS_SANDBOX' => $this->setApnsSandbox($notification, $pushData),
                'APNS' => $this->setApns($notification, $pushData),
                'GCM' => $this->setGcm($notification, $pushData),
            ]);

            try {
                if ($notification->platform_type === 'app' || $notification->platform_type === 'web') {
                    NotifyHelper::sns(config('notify.sns'), $messageJSON, $this->topicName);
                }

                $notification->update(['push_notifications_status' => NotificationsStatus::SUCCESS]);
            } catch (\Throwable $exception) {
                $notification->update(['push_notifications_status' => NotificationsStatus::FAILED]);

                \Log::alert($exception->getMessage());
            }
        });
    }

    /**
     * @param NotificationsModel $notification
     * @param array $pushData
     * @return string
     */
    public function setApnsSandbox(NotificationsModel $notification, array $pushData): string
    {
        return $this->encode([
            'aps' => [
                'alert' => [
                    'title' => $notification->message,
                    'body' => $notification->message_body,
                    'badge' => '1',
                ],
                'sound' => 'default',
                'content-available' => '1',
                'mutable-content' => '1',
                'message' => [
                    // 'attachment' => $attachment,//推播圖片影片
                    'push_type' => $notification->type, //活動通知、系統公告、文章、影音、搖一搖。
                    'category_id' => $notification->categories_id,
                    'category_name' => $pushData['announcement']['category_name'],
                    'article_id' => $pushData['announcement']['articles_id'],
                    'title' => $pushData['announcement']['title'],
                    'publish_date' => $pushData['announcement']['publish'],
                    'content_type' => $notification->content_type == 0 ? '2' : $notification->content_type, // 1: webView 2: native
                    'url' => $pushData['url'],
                    'api_url' => $pushData['api_url'],
                ],
            ],
        ]);
    }

    /**
     * @param NotificationsModel $notification
     * @param array $pushData
     * @return string
     */
    public function setApns(NotificationsModel $notification, array $pushData): string
    {
        return $this->encode([
            'aps' => [
                'alert' => [
                    'title' => $notification->message,
                    'body' => $notification->message_body,
                    'badge' => '1',
                ],
                'sound' => 'default',
                'content-available' => '1',
                'mutable-content' => '1',
                'message' => [
                    // 'attachment' => $attachment,//推播圖片影片
                    'push_type' => $notification->type, //活動通知、系統公告、文章、影音、搖一搖。
                    'category_id' => 'topic',
                    'category_name' => $pushData['announcement']['category_name'],
                    'article_id' => $pushData['announcement']['articles_id'],
                    'title' => $pushData['announcement']['title'],
                    'publish_date' => $pushData['announcement']['publish'],
                    'content_type' => $notification->content_type == 0 ? '2' : $notification->content_type, //1 webView 2 native
                    'url' => $pushData['url'],
                    'api_url' => $pushData['api_url'],
                ],
            ],
        ]);
    }

    /**
     * @param NotificationsModel $notification
     * @param array $pushData
     * @return string
     */
    public function setGcm(NotificationsModel $notification, array $pushData): string
    {
        return $this->encode([
            'data' => [
                // 'attachment' => $attachment,//推播圖片影片
                'push_type' => $notification->type, //活動通知、系統公告、文章、影音、搖一搖。
                'category_id' => 'topic',
                'category_name' => $pushData['announcement']['category_name'],
                'article_id' => $pushData['announcement']['articles_id'],
                'bigtitle' => $notification->message,
                'title' => $notification->message_body,
                'title_fa' => $pushData['announcement']['title'],
                'publish_date' => $pushData['announcement']['publish'],
                'content_type' => $notification->content_type == 0 ? '2' : $notification->content_type, //1 webView 2 native
                'url' => $pushData['url'],
                'api_url' => $pushData['api_url'],
            ],
        ]);
    }

    /**
     * @param array $data
     * @return string
     */
    public function encode(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param NotificationsModel $notification
     * @return array
     */
    public function factory(NotificationsModel $notification): array
    {
        $url = $notification->url;

        $announcement = collect([]);
        switch ($notification->type) {
            case NotificationsType::ARTICLE:
                if (!$notification->category || !$notification->category_en || !$notification->categories_id) {
                    return [];
                }

                $notification->load(['article']);

                if ($notification->article !== null) {
                    $apiUrl = "article/{$notification->category_en}/{$notification->article->articles_id}";
                    $url = config('app.web_url') . "/{$notification->category_en}/{$notification->article->articles_id}";

                    $announcement = $notification->article;
                    $announcement->category_en_name = $notification->category_en;
                    $announcement->category_name = $notification->category;
                }
                break;
            case NotificationsType::SYSTEM:
                $notification->load(['system']);

                if ($notification->system !== null) {
                    $apiUrl = "api_index/system_announcement_detail/{$notification->source_id}";

                    $announcement->put('articles_id', $notification->source_id);
                    $announcement->put('publish', $notification->prepush);
                    $announcement->put('category_en_name', '');
                    $announcement->put('category_name', '');
                    $announcement->put('title', $notification->message);
                }
                break;
            case NotificationsType::ACTIVITY:
                $notification->load(['activity']);

                if ($notification->activity !== null) {
                    $apiUrl = "api_index/activities_announcement_detail/{$notification->source_id}";

                    $announcement->put('articles_id', $notification->source_id);
                    $announcement->put('publish', $notification->prepush);
                    $announcement->put('category_en_name', '');
                    $announcement->put('category_name', '');
                    $announcement->put('title', $notification->message);
                }
                break;
            case NotificationsType::SHAKE:
                $notification->load(['shake']);

                if ($notification->shake !== null) {
                    $apiUrl = "shake/get/{$notification->source_id}";

                    $announcement->put('articles_id', $notification->source_id);
                    $announcement->put('publish', $notification->prepush);
                    $announcement->put('category_en_name', '');
                    $announcement->put('category_name', '');
                    $announcement->put('title', $notification->message);
                }
                break;
        }

        return [
            'api_url' => $apiUrl ?? '',
            'url' => $url ?? $notification->url,
            'announcement' => $announcement->toArray(),
        ];
    }
}
