<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DepartmentModel;

class CreateDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            '董事會',
            '總經理室',
            '頻道發行部',
            '財務部',
            '新聞部',
            '人力資源部',
            '管理部',
            '版權業務部',
            '工程技術部',
            '海外發展部',
            '法律事務部',
            '節目部',
            '策略與新創事業部',
            '品牌公關部',
            '業務部',
            '數位事業部',
            '製作資源部',
            '趨勢發展部',
        ];

        foreach($datas as $data){
            DepartmentModel::create(['department_name'=>$data]);
        }
    }
}
