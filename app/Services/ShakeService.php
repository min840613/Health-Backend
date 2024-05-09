<?php

namespace App\Services;

use App\Models\App\ShakeMemberModel;
use App\Models\App\ShakeModel;
use App\Models\User;
use App\Repositories\ShakeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Class ShakeService
 * @package App\Services
 */
class ShakeService
{
    /** @var int */
    private int $perPage = 20;

    /** @var ShakeRepository */
    private ShakeRepository $repository;

    /**
     * ShakeService constructor.
     * @param ShakeRepository $repository
     */
    public function __construct(ShakeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function find(array $data): LengthAwarePaginator
    {
        $shakes = $this->repository->find($data, $this->perPage);

        $members = ShakeMemberModel::selectRaw('count(shake_id) as total, shake_id')
            ->whereIn('shake_id', $shakes->pluck('shake_id')->toArray())
            ->groupBy('shake_id')
            ->get()
            ->keyBy('shake_id');

        $shakes->transform(function ($shake) use ($members) {
            $shake->setRelation('membersCount', $members[$shake->shake_id]->total ?? 0);
            return $shake;
        });

        return $shakes;
    }

    /**
     * @param string $start
     * @param string $end
     * @param string|null $id
     * @throws ValidationException
     */
    public function validateTimes(string $start, string $end, string $id = null): void
    {
        $validate = $this->repository->validateTimes(Carbon::parse($start)->toDateTimeString(), Carbon::parse($end)->toDateTimeString(), $id);

        if (count($validate) > 0) {

            $messageArray = [];
            foreach ($validate as $validateData) {
                $messageArray[] = $validateData['shake_id'];
            }
            
            throw ValidationException::withMessages(['shake_time_end' => '您指定活動時段有已發佈的活動, 請參考活動ID : ' . implode(',', $messageArray)]);
        }
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
            $this->repository->store($data, $user);
            \DB::commit();
        } catch (\Throwable $exception) {
            \DB::rollBack();
            \Log::alert($exception);
        }
    }

    /**
     * @param ShakeModel $model
     * @param array $data
     * @param User $user
     * @throws \Throwable
     */
    public function update(ShakeModel $model, array $data, User $user): void
    {
        \DB::beginTransaction();

        try {
            $model->fill($data);
            $model->updated_user = $user->name;
            $model->save();
            \DB::commit();
        } catch (\Throwable $exception) {
            \DB::rollBack();
            \Log::alert($exception);
        }
    }
}
