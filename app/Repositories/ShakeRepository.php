<?php

namespace App\Repositories;

use App\Models\App\ShakeModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use DB;

/**
 * Class ShakeRepository
 * @package App\Services
 */
class ShakeRepository
{
    /** @var ShakeModel */
    private ShakeModel $model;

    /**
     * ShakeRepository constructor.
     * @param ShakeModel $model
     */
    public function __construct(ShakeModel $model)
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
            ->when(!empty($data['search_shake_time_start']), function ($query) use ($data) {
                $query->where('shake_time_start', '>=', $data['search_shake_time_start']);
            })
            ->when(!empty($data['search_shake_time_end']), function ($query) use ($data) {
                $query->where('shake_time_start', '<=', Carbon::parse($data['search_shake_time_end'])->endOfDay());
            })
            ->when(!empty($data['search_shake_id']), function ($query) use ($data) {
                $query->where('shake_id', $data['search_shake_id']);
            })
            ->when(!empty($data['search_shake_title']), function ($query) use ($data) {
                $query->where('shake_title', 'like', "%{$data['search_shake_title']}%");
            })
            ->orderByDesc('shake_id')
            ->paginate($perPage)
            ->appends($data);
    }

    /**
     * @param string $start
     * @param string $end
     * @param string $id
     * @return Collection
     */
    public function validateTimes(string $start, string $end, string $id = null): Collection
    {
        return $this->model->publish()
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($query) use ($start, $end) {
                    $query->whereBetween('shake_time_start', [$start, $end]);
                })->orWhere(function ($query) use ($start, $end) {
                    $query->where('shake_time_start', '<=', $start);
                    $query->where('shake_time_end', '>=', $end);
                })->orWhere(function ($query) use ($start, $end) {
                    $query->whereBetween('shake_time_end', [$start, $end]);
                });
            })->when($id, function ($query, $id) {
                $query->whereNotIn('shake_id', [$id]);
            })->get();
    }

    /**
     * @param array $data
     * @param User $user
     */
    public function store(array $data, User $user): void
    {
        $model = new $this->model();
        $model->fill($data);
        $model->created_user = $user->name;
        $model->updated_user = $user->name;
        $model->save();
    }
}
