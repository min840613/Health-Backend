<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Masters\DivisionsModel;
use Illuminate\Support\Carbon;

class DivisionsModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $news = DivisionsModel::create([
            'status' => '1',
            'type' => '2',
            'name' => '科別總覽',
            // icon、icon hover 這兩個圖檔，會在測試站和正式站跑完seeder後，請後台操作者，編輯一次正確的圖片
            'icon' => 'https://static.tvbs.com.tw/health2.0-pre/images/uploads/2023/07/20230710094920-sRCKTU92.svg',
            'icon_hover' => 'https://static.tvbs.com.tw/health2.0-pre/images/uploads/2023/07/20230710094920-sRCKTU92.svg',
            'created_at' => Carbon::now(),
            'created_user' => 'dev001',
            'updated_user' => 'dev001'
        ]);
    }
}
