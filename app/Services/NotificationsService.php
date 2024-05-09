<?php

namespace App\Services;

use App\Enums\NotificationsType;
use App\Enums\ShakeContentType;
use App\Models\App\ActivitiesAnnouncementModel;
use App\Models\App\NotificationsModel;
use App\Models\App\ShakeModel;
use App\Models\App\SystemAnnouncementModel;
use App\Models\Articles\ArticleModel;
use App\Models\User;
use App\Repositories\NotificationsRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotificationsService
 * @package App\Services
 */
class NotificationsService
{
    /** @var int */
    private int $perPage = 20;

    /** @var NotificationsRepository */
    private NotificationsRepository $repository;

    /** @var string[] */
    public const PUSH_STATUS_OPTIONS = [
        -1 => '全部',
        1 => '尚未推播',
        2 => '推播成功',
        3 => '取消推播',
        4 => '推播失敗',
        5 => '推播發送中',
    ];

    /** @var string[] */
    public const TYPE_OPTIONS = [
        -1 => '全部',
        1 => '文章',
        2 => '影片',
        3 => '訊息通知',
        4 => '活動公告',
        5 => 'APP搖一搖',
        6 => '會員專屬',
        7 => '我的通知',
    ];

    /**
     * ArticlesService constructor.
     * @param NotificationsRepository $repository
     */
    public function __construct(NotificationsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function find(array $data): LengthAwarePaginator
    {
        return $this->repository->find($data, $this->perPage);
    }

    /**
     * @param int $articleId
     * @return bool
     */
    public function validateRepeatArticle(int $articleId): bool
    {
        $validate = $this->repository->validateRepeatArticle($articleId);

        return $validate->isNotEmpty();
    }

    /**
     * @param array $data
     * @param User $user
     * @throws \Throwable
     */
    public function store(array $data, User $user): void
    {
        \DB::beginTransaction();

        try {
            $relation = $this->announcementCreate($data['type'], $data, $user);

            $this->repository->store($relation->id, $data, $user);
            \DB::commit();
        } catch (\Throwable $exception) {
            \DB::rollBack();
            \Log::alert($exception);
        }
    }

    /**
     * @param $request
     * @return array
     */
    public function getBasicData($request): array
    {
        switch (true) {
            case $request->input('article_id'):
                $article = ArticleModel::find($request->input('article_id'));

                return [
                    'prepush' => now()->toDateTimeString(),
                    'message_body' => strip_tags($article->title),
                ];
                break;
            case $request->input('shake_id'):
                $shake = ShakeModel::find($request->input('shake_id'));

                return [
                    'prepush' => now()->toDateTimeString(),
                    'message_body' => $shake->content,
                ];
                break;
            default:
                return [];
                break;
        }
    }

    /**
     * @param int $type
     * @param array $data
     * @param User $user
     * @return Model
     */
    private function announcementCreate(int $type, array &$data, User $user): Model
    {
        switch ($type) {
            case NotificationsType::ARTICLE:
                $article = ArticleModel::find($data['article_id']);
                $article->id = $article->articles_id;

                $mainCategory = $article->mainCategory;

                $data['categories_id'] = $mainCategory->categories_id;
                $data['category_en'] = $mainCategory->en_name;
                $data['category'] = $mainCategory->name;
                $data['image'] = $article->image;

                return $article;
                break;
            case NotificationsType::SYSTEM:
                return SystemAnnouncementModel::create([
                    'title' => $data['message'],
                    'content' => $data['message_body'],
                    'image_url' => $data['image'],
                    'created_user' => $user->name,
                    'updated_user' => $user->name,
                ]);
                break;
            case NotificationsType::ACTIVITY:
                return ActivitiesAnnouncementModel::create([
                    'title' => $data['message'],
                    'content' => $data['message_body'],
                    'image_url' => $data['image'],
                    'created_user' => $user->name,
                    'updated_user' => $user->name,
                ]);
                break;
            case NotificationsType::SHAKE:
                $shake = ShakeModel::find($data['shake_id']);
                $shake->id = $shake->shake_id;

                if ($shake->shake_content_type === ShakeContentType::ARTICLE) {
                    \Log::debug($shake->article);
                    $data['image'] = $shake->article->image;
                }

                return $shake;
                break;
            default:
                \Log::alert(__METHOD__ . ' error.');
                break;
        }
    }

    /**
     * @param NotificationsModel $model
     */
    public function appendRelationColumn(NotificationsModel $model): void
    {
        $content = '';
        switch ($model->type) {
            case NotificationsType::ARTICLE:
                $content = $model->article->article_content;
                break;
            case NotificationsType::SYSTEM:
                $content = $model->system->content;
                break;
            case NotificationsType::ACTIVITY:
                $content = $model->activity->content;
                break;
        }

        $model->content = $content;
    }

    /**
     * @param NotificationsModel $notification
     * @param array $data
     * @param User $user
     * @throws \Throwable
     */
    public function update(NotificationsModel $notification, array $data, User $user): void
    {
        \DB::beginTransaction();

        try {
            $this->announcementUpdate($notification->type, $notification, $data, $user);

            $notification->fill($data);
            $notification->updated_user = $user->name;
            $notification->save();
            \DB::commit();
        } catch (\Throwable $exception) {
            \DB::rollBack();
            \Log::alert($exception);
        }
    }

    /**
     * @param string $type
     * @param NotificationsModel $notification
     * @param array $data
     * @param User $user
     */
    private function announcementUpdate(string $type, NotificationsModel $notification, array $data, User $user)
    {
        $content = isset($data['content_type']) && $data['content_type'] === '2' ? $data['content'] : '';
        switch ($type) {
            case NotificationsType::ARTICLE:
            case NotificationsType::SHAKE:
                break;
            case NotificationsType::SYSTEM:
                $model = SystemAnnouncementModel::find($notification->source_id);
                $model->fill([
                    'content' => $content,
                    'image_url' => $data['image'],
                    'updated_user' => $user->name,
                ]);
                $model->save();
                break;
            case NotificationsType::ACTIVITY:
                $model = ActivitiesAnnouncementModel::find($notification->source_id);
                $model->fill([
                    'content' => $content,
                    'image_url' => $data['image'],
                    'updated_user' => $user->name,
                ]);
                $model->save();
                break;
            default:
                \Log::alert(__METHOD__ . ' error.');
                break;
        }
    }
}
