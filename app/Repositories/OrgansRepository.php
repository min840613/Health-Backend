<?php

namespace App\Repositories;

use App\Models\Encyclopedia\OrgansModel;
use Illuminate\Support\Collection;

/**
 * Class OrgansRepository
 * @package App\Repositories
 */
class OrgansRepository
{
    /** @var OrgansModel */
    private OrgansModel $model;

    /**
     * OrgansRepository constructor.
     * @param OrgansModel $model
     */
    public function __construct(OrgansModel $model)
    {
        $this->model = $model;
    }

    /**
     * 建立資料
     * 
     * @param array $data
     */
    public function create(array $data): void
    {
        $this->model->body_id = $data['body_id'];
        $this->model->icon = $data['icon_full_url'];
        $this->model->icon_android = $data['icon_android_full_url'];
        $this->model->icon_ios = $data['icon_ios_full_url'];
        $this->model->name   = $data['name'];
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
            'status' => $data['status'],
            'updated_user' => $data['updated_user'],
        ];

        if (isset($data['icon_full_url']) && !empty($data['icon_full_url'])) {
            $updateData['icon'] = $data['icon_full_url'];
        }

        if (isset($data['icon_android_full_url']) && !empty($data['icon_android_full_url'])) {
            $updateData['icon_android'] = $data['icon_android_full_url'];
        }

        if (isset($data['icon_ios_full_url']) && !empty($data['icon_ios_full_url'])) {
            $updateData['icon_ios'] = $data['icon_ios_full_url'];
        }

        $row = $this->model::find($data['id']);
        $row->update($updateData);
    }

    /**
     * 找尋單筆資料
     * 
     * @param int $id
     * @return OrgansModel
     */
    public function find(int $id): OrgansModel
    {
        return $this->model->where('id', $id)->first();
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
