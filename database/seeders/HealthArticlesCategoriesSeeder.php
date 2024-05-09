<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Encyclopedia\ArticlesCategoriesModel;

class HealthArticlesCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); //禁用外键约束
        DB::table('health_articles_categories')->truncate();

        DB::transaction(function () {
            $result = DB::connection('mysql_tvbs_v4')->table('health_articles_categories')->get();
            $result->transform(function($i) {
                return (array)$i;
            });
            $result = $result->toArray();
            foreach($result as $k => $v){

                if($result[$k]['publish_time'] == '0000-00-00 00:00:00'){
                    $result[$k]['publish_time'] = null;
                }
                if($result[$k]['updated_user'] == NULL){
                    $result[$k]['updated_user'] = '';
                }
                if($result[$k]['updated_time'] == '0000-00-00 00:00:00'){
                    $result[$k]['updated_time'] = null;
                }

                $result[$k]['created_at'] = $result[$k]['created_time'];
                $result[$k]['updated_at'] = $result[$k]['updated_time'];
                unset($result[$k]['created_time'],$result[$k]['updated_time']);
            }
            ArticlesCategoriesModel::insert($result);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); //啟用外键约束
    }
}
