<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Exception;

use App\Models\Categories\MainCategoriesModel;
use App\Models\Categories\SubCategoriesModel;
use App\Models\Articles\ArticleCategoriesMappingsModel;
use DB;

class ArticlesCategoriesMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articlesCategoriesMapping:year {year}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '轉換舊文章類別，變數為年份，將決定須讀取哪個csv檔案，範例檔案：{year}_articles_categories_mapping.csv';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $year = $this->argument('year');
        $documentPath = public_path() . "/documents/".$year."_articles_categories_mapping.csv";

        try{
            $csvData = [];
            if (($open = fopen($documentPath, "r")) !== FALSE) {
                while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                    $csvData[] = $data;
                }
                fclose($open);
            }

            $mainCategories = MainCategoriesModel::all()
                                    ->pluck('name', 'categories_id')
                                    ->toArray();

            $subCategories = [];

            foreach($mainCategories as $key => $val){
                $subCategories[$key] = SubCategoriesModel::where('categories_id', $key)
                                            ->get()
                                            ->pluck('name', 'sub_categories_id')
                                            ->toArray();
            }

            $insertCount = 0;
            foreach($csvData as $key => $val){
                $articleId = trim(substr(strrchr($val[1], "/"), 1));
                if(!$articleId || !is_numeric($articleId)){
                    echo "article id：".$articleId." 錯誤\n";
                    continue;
                }

                $mainCategoryId = array_search(trim($val[2]), $mainCategories);
                if(!$mainCategoryId){
                    echo "article id：".$articleId." 無主分類\n";
                    continue;
                }

                ArticleCategoriesMappingsModel::where('article_id', $articleId)->delete();

                $insertFirstData = [];
                $insertFirstData = [
                    'article_id' => $articleId,
                    'category_id' => $mainCategoryId,
                    'sort' => 0
                ];
                ArticleCategoriesMappingsModel::create($insertFirstData);

                $sql_categories_id = ',';
                $sql = 'Update health_articles set categories_main='.$mainCategoryId;
                $sql_categories_id .= $mainCategoryId.',';

                $subCategoryId = array_search(trim($val[3]), $subCategories[$mainCategoryId]);
                if($subCategoryId){

                    $sql .= ', sub_categories_id='.$subCategoryId;

                    $insertFirstData = [];
                    $insertFirstData = [
                        'article_id' => $articleId,
                        'category_id' => $subCategoryId,
                        'sort' => 0,
                        'parent' => $mainCategoryId
                    ];
                    ArticleCategoriesMappingsModel::create($insertFirstData);
                }else{
                    $sql .= ', sub_categories_id = NULL';
                }


                $subMainCategoryId = array_search(trim($val[4]), $mainCategories);
                if($subMainCategoryId){
                    $sql_categories_id .= $subMainCategoryId.',';

                    $subSubCategoryId = array_search(trim($val[5]), $subCategories[$subMainCategoryId]);
                    $insertSecondData = [];
                    $insertSecondData = [
                        'article_id' => $articleId,
                        'category_id' => $subMainCategoryId,
                        'sort' => 1
                    ];
                    ArticleCategoriesMappingsModel::create($insertSecondData);

                    if($subSubCategoryId){
                        $insertSecondData = [];
                        $insertSecondData = [
                            'article_id' => $articleId,
                            'category_id' => $subSubCategoryId,
                            'sort' => 1,
                            'parent' => $subMainCategoryId
                        ];
                        ArticleCategoriesMappingsModel::create($insertSecondData);
                    }
                }

                $sql .= ', categories_id="'.$sql_categories_id.'"';
                $sql .= ' where articles_id='.$articleId;

                DB::connection('mysql_tvbs_v4')->statement($sql);

                $insertCount ++;
            }

            echo '修改文章篇數為：'.$insertCount."\n";

            return Command::SUCCESS;
        }catch(Exception $e){
            echo '錯誤: '.$e->getMessage()."\n";
            return Command::FAILURE;
        }
    }
}
