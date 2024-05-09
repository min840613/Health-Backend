<?php

namespace App\Repositories;

use App\Models\Masters\InstitutionsModel;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class InstitutionsRepository
 * @package App\Repositories
 */
class InstitutionsRepository
{
    /** @var InstitutionsModel */
    private InstitutionsModel $model;

    /**
     * InstitutionsRepository constructor.
     * @param InstitutionsModel $model
     */
    public function __construct(InstitutionsModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $data
     * @return Collection
     */
    public function lists(array $data): Collection
    {
        return $this->model::query()
            ->when(!empty($data['filter_name']), function ($query) use ($data) {
                $query->where(function ($query) use ($data) {
                    $query->where('name', 'like', "%{$data['filter_name']}%")
                        ->orWhere('nick_name', 'like', "%{$data['filter_name']}%");
                });
            })
            ->orderBy('sort')
            ->get();
    }

    /**
     * @param string $id
     * @return InstitutionsModel|null
     */
    public function getById(string $id): ?InstitutionsModel
    {
        return $this->model::find($id);
    }

    /**
     * @param string $enName
     * @param array $expectIds
     * @return Collection
     */
    public function getByEnName(string $enName, array $expectIds): Collection
    {
        return $this->model::where('en_name', $enName)
            ->when(!empty(array_filter($expectIds)), function ($query) use ($expectIds) {
                $query->whereNotIn('id', $expectIds);
            })
            ->get();
    }

    /**
     * 建立資料
     *
     * @param array $data
     */
    public function create(array $data): void
    {
        $this->model->fill($data);
        $this->model->save();
    }

    /**
     * 更新資料
     *
     * @param array $data
     */
    public function update(array $data): void
    {
        $row = $this->model::find($data['id']);

        $row->update(\Arr::except($data, ['id']));
    }

    /**
     * 儲存排序
     *
     * @param int $id
     * @param int $sort
     */
    public function saveSort(int $id, int $sort): void
    {
        $this->model::find($id)->update(['sort' => $sort]);
    }
}
