<?php

namespace App\Services;

use App\Models\Deepq\KeywordModel;
use App\Models\User;
use App\Repositories\DeepqKeywordRepository;
use Illuminate\Support\Collection;

/**
 * Class DeepqKeywordService
 * @package App\Services
 */
class DeepqKeywordService
{
    private DeepqKeywordRepository $repository;

    public function __construct(DeepqKeywordRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Collection
     */
    public function lists(): Collection
    {
        return $this->repository->lists();
    }

    /**
     * @param array $data
     * @param KeywordModel|null $model
     * @return string[]
     */
    public function validate(array $data, ?KeywordModel $model): array
    {
        $containKeywords = $this->repository->containKeywords($data['start_at'], $data['end_at'], $model->id ?? null);

        if ($containKeywords->isNotEmpty()) {
            return ['status' => '時間區間不可與其餘關鍵字重疊'];
        }

        return [];
    }

    /**
     * @param array $data
     * @param User $user
     * @return void
     * @throws \Throwable
     */
    public function create(array $data, User $user): void
    {
        $data['created_user'] = $user->name;
        $data['updated_user'] = $user->name;

        $this->repository->create($data);
    }

    /**
     * @param KeywordModel $model
     * @param array $data
     * @param User $user
     * @return void
     * @throws \Throwable
     */
    public function update(KeywordModel $model, array $data, User $user): void
    {
        $data['updated_user'] = $user->name;

        $this->repository->update($model, $data);
    }

    /**
     * @param KeywordModel $model
     * @param User $user
     * @return void
     * @throws \Throwable
     */
    public function delete(KeywordModel $model, User $user): void
    {
        $this->repository->delete($model, $user);
    }
}
