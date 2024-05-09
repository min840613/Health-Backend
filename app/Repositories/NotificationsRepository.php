<?php

namespace App\Repositories;

use App\Enums\NotificationsType;
use App\Models\App\NotificationsModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class NotificationsRepository
 * @package App\Services
 */
class NotificationsRepository
{
    /** @var NotificationsModel */
    private NotificationsModel $model;

    /**
     * ArticlesRepository constructor.
     * @param NotificationsModel $model
     */
    public function __construct(NotificationsModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $data
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function find(array $data, int $perPage): LengthAwarePaginator
    {
        return $this->model
            ->when(!empty($data['push_date_start']), function ($query) use ($data) {
                $query->where('prepush', '>=', $data['push_date_start']);
            })
            ->when(!empty($data['push_date_end']), function ($query) use ($data) {
                $query->where('prepush', '<=', Carbon::parse($data['push_date_end'])->endOfDay());
            })
            ->when(isset($data['push_notifications_status']) && $data['push_notifications_status'] != -1, function ($query) use ($data) {
                $query->where('push_notifications_status', $data['push_notifications_status']);
            })
            ->when(isset($data['type']) && $data['type'] != -1, function ($query) use ($data) {
                $query->where('type', $data['type']);
            })
            ->when(!empty($data['id']), function ($query) use ($data) {
                $query->where('id', $data['id']);
            })
            ->when(!empty($data['message']), function ($query) use ($data) {
                $query->where('message', 'like', "%{$data['message']}%");
            })
            ->when(!empty($data['created_user']), function ($query) use ($data) {
                $query->where('created_user', 'like', "%{$data['created_user']}%");
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($data);
    }

    /**
     * @param int $relationId
     * @param array $data
     * @param User $user
     */
    public function store(int $relationId, array $data, User $user): void
    {
        $this->model->create([
            'source_id' => $relationId,
            'type' => $data['type'],
            'categories_id' => $data['categories_id'] ?? 0,
            'category_en' => $data['category_en'] ?? '',
            'category' => $data['category'] ?? '健康',
            'platform_type' => 'app',
            'message' => $data['message'],
            'message_body' => $data['message_body'],
            'content_type' => $data['content_type'] ?? 2,
            'image' => $data['image'] ?? '',
            'url' => $data['url'] ?? null,
            'prepush' => $data['prepush'],
            'created_user' => $user->name,
            'updated_user' => $user->name,
        ]);
    }

    /**
     * @param int $id
     * @return Collection
     */
    public function validateRepeatArticle(int $id): Collection
    {
        return $this->model->where('source_id', $id)->where('type', NotificationsType::ARTICLE)->get();
    }
}
