<?php

namespace App\Repositories;

use App\Models\Deepq\KeywordModel;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Class DeepqKeywordRepository
 * @package App\Repositories
 */
class DeepqKeywordRepository
{
    /** @var KeywordModel */
    private KeywordModel $model;

    /**
     * @param KeywordModel $model
     */
    public function __construct(KeywordModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function lists(): Collection
    {
        return $this->model::with(['questions'])->get();
    }

    /**
     * @param string $start
     * @param string $end
     * @param int|null $id
     * @return Collection
     */
    public function containKeywords(string $start, string $end, int $id = null): Collection
    {
        return $this->model::where(function ($query) use ($start, $end) {
            $query->where(function ($query) use ($start) {
                $query->where('start_at', '<=', $start)->where('end_at', '>=', $start);
            })->orWhere(function ($query) use ($end) {
                $query->where('start_at', '<=', $end)->where('end_at', '>=', $end);
            })->orWhere(function ($query) use ($start, $end) {
                $query->where('start_at', '>=', $start)->where('end_at', '<=', $end);
            });
        })->when($id, function ($query, $id) {
            $query->whereNot('id', $id);
        })->get();
    }

    /**
     * @param array $data
     * @return void
     * @throws \Throwable
     */
    public function create(array $data): void
    {
        \DB::transaction(function () use ($data) {
            $model = $this->model::create($data);

            foreach ($data['question'] as $index => $question) {
                $model->questions()->create(['question' => $question, 'sort' => $index + 1]);
            }
        });
    }

    /**
     * @param KeywordModel $model
     * @param array $data
     * @return void
     * @throws \Throwable
     */
    public function update(KeywordModel $model, array $data): void
    {
        \DB::transaction(function () use ($model, $data) {
            $model->fill($data);
            $model->save();

            $model->questions()->delete();
            foreach ($data['question'] as $index => $question) {
                $model->questions()->create(['question' => $question, 'sort' => $index + 1]);
            }
        });
    }

    /**
     * @param KeywordModel $model
     * @param User $user
     * @return void
     * @throws \Throwable
     */
    public function delete(KeywordModel $model, User $user): void
    {
        \DB::transaction(function () use ($model, $user) {
            $model->deleted_user = $user->name;
            $model->save();

            $model->delete();
        });
    }
}
