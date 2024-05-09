<?php

namespace App\Services;

use App\Repositories\AiWizeRepository;
use Illuminate\Support\Collection;
use App\Models\Aiwize\AiwizeModel;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class AiWizeService
 * @package App\Services
 */
class AiWizeService
{
    private AiWizeRepository $repository;

    public function __construct(AiWizeRepository $repository)
    {
        $this->repository = $repository;
    }

     /**
     * @return LengthAwarePaginator
     */
    public function lists(): LengthAwarePaginator
    {
        return $this->repository->lists();
    }

    /**
     * @param AiwizeModel $model
     * @param User $user
     * @return void
     * @throws \Throwable
     */
    public function delete(AiwizeModel $model, User $user): void
    {
        $this->repository->delete($model, $user);
    }
}
