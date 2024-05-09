<?php

namespace App\Repositories;

use App\Models\Encyclopedia\SicknessModel;
use DB;

/**
 * Class SicknessRepository
 * @package App\Repositories
 */
class SicknessRepository
{
    /** @var SicknessModel */
    private SicknessModel $model;

    /**
     * SicknessRepository constructor.
     * @param SicknessModel $model
     */
    public function __construct(SicknessModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $id
     * @param int $sort
     */
    public function saveSort(int $id, int $sort): void
    {
        $this->model::find($id)->update(['sort' => $sort]);
    }

    /**
     * @param array $sickness
     * 建立疾病資料以及關聯表
     */
    public function create($data)
    {

        DB::beginTransaction();
        $this->model->name   = $data['name'];
        $this->model->status = $data['status'];
        $this->model->created_user = $data['created_user'];
        $this->model->updated_user = $data['updated_user'];
        $this->model->save();
        $this->model->organs()->attach($data['create_organ_id_array']);
        DB::commit();
    }

    /**
     * @param array $sickness
     * 更新疾病資料以及關聯表
     */
    public function update($data)
    {
        $updateData = [
            'name' => $data['name'],
            'status' => $data['status'],
            'updated_user' => $data['updated_user'],
        ];

        DB::beginTransaction();
        $row = $this->model::find($data['id']);
        $row->update($updateData);
        $row->organs()->sync($data['create_organ_id_array']);
        DB::commit();
    }

    public function find($id)
    {
        return $this->model->with(['organs'])->where('id', $id)->first();
    }
}
