<?php

namespace App\Repositories;

use App\Models\Aiwize\AiwizeModel;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class AiWizeRepository
 * @package App\Repositories
 */
class AiWizeRepository
{
     /** @var AiwizeModel */
    private AiwizeModel $model;

     /**
     * @param AiwizeModel $model
     */
    public function __construct(AiwizeModel $model)
    {
        $this->model = $model;
    }

     /**
     * @return LengthAwarePaginator
     */
    public function lists(): LengthAwarePaginator
    {
        return $this->model::orderBy('id', 'desc')->paginate(10);
    }

    /**
     * @param AiwizeModel $model
     * @param User $user
     * @return void
     * @throws \Throwable
     */
    public function delete(AiwizeModel $model, User $user): void
    {
        \DB::transaction(function () use ($model, $user) {
            $model->deleted_user = $user->name;
            $model->save();

            $model->delete();
        });
    }
}
