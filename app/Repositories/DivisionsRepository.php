<?php

namespace App\Repositories;

use App\Models\Masters\DivisionsModel;
use Illuminate\Database\Eloquent\Collection;
use DB;

/**
 * Class DivisionsRepository
 * @package App\Repositories
 */
class DivisionsRepository
{
    /** @var DivisionsModel */
    private DivisionsModel $model;

    /**
     * DivisionsRepository constructor.
     * @param DivisionsModel $model
     */
    public function __construct(DivisionsModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model::query()->orderByDesc('type')->orderBy('sort')->get();
    }

    /**
     * @param string $id
     * @return DivisionsModel|null
     */
    public function getById(string $id): ?DivisionsModel
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
        $this->model->icon = $data['icon_full_url'];
        $this->model->icon_hover = $data['icon_hover_full_url'];
        $this->model->icon_android = $data['icon_android_full_url'] ?? null;
        $this->model->icon_android_hover = $data['icon_android_hover_full_url'] ?? null;
        $this->model->icon_ios = $data['icon_ios_full_url'] ?? null;
        $this->model->icon_ios_hover = $data['icon_ios_hover_full_url'] ?? null;
        $this->model->name   = $data['name'];
        $this->model->en_name   = $data['en_name'];
        $this->model->status = $data['status'];
        $this->model->created_user = $data['created_user'];
        $this->model->updated_user = $data['updated_user'];
        $this->model->save();
    }

    /**
     * 更新資料
     *
     * @param array $data
     */
    public function update(array $data): void
    {
        $updateData = [
            'name' => $data['name'],
            'en_name' => $data['en_name'],
            'status' => $data['status'],
            'updated_user' => $data['updated_user'],
        ];

        if (isset($data['icon_full_url']) && !empty($data['icon_full_url'])) {
            $updateData['icon'] = $data['icon_full_url'];
        }

        if (isset($data['icon_hover_full_url']) && !empty($data['icon_hover_full_url'])) {
            $updateData['icon_hover'] = $data['icon_hover_full_url'];
        }

        if (isset($data['icon_android_full_url']) && !empty($data['icon_android_full_url'])) {
            $updateData['icon_android'] = $data['icon_android_full_url'];
        }

        if (isset($data['icon_android_hover_full_url']) && !empty($data['icon_android_hover_full_url'])) {
            $updateData['icon_android_hover'] = $data['icon_android_hover_full_url'];
        }

        if (isset($data['icon_ios_full_url']) && !empty($data['icon_ios_full_url'])) {
            $updateData['icon_ios'] = $data['icon_ios_full_url'];
        }

        if (isset($data['icon_ios_hover_full_url']) && !empty($data['icon_ios_hover_full_url'])) {
            $updateData['icon_ios_hover'] = $data['icon_ios_hover_full_url'];
        }


        $row = $this->model::find($data['id']);
        $row->update($updateData);
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
