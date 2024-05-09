<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Carbon;
use App\Models\HealthExhibitionListModel;
use App\Models\Categories\MenuListModel;
use App\Models\Articles\ArticleModel;
use App\Models\Masters\MastersModel;

class SitemapProduce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:produce';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produce Sitemap To AWS S3';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $allSiteMap = [];

        $domainUrl = "https://health.tvbs.com.tw";
        if (!env('APP_WEB_URL') || stripos(env('APP_WEB_URL'), '-pre') || stripos(env('APP_WEB_URL'), 'local')) {
            $domainUrl = "https://health-pre.tvbs.com.tw";
        }

        //策展xml
        $sitemap_file_name_curation = 'curation_sitemap.xml';
        $exhibitions = $this->getHealthCuration();

        $urls_arr_exhibition = [];
        $updated_at_arr_exhibition = [];
        $image_arr_exhibition = [];
        foreach ($exhibitions as $exhibition) {
            $urls_arr_exhibition[] = $exhibition['web_url'];
            $updated_at_arr_exhibition[] = $exhibition['updated_at'];
            $image_arr_exhibition[] = $exhibition['image'];
        }
        $view_data_exhibition['urls_arr'] = $urls_arr_exhibition;
        $view_data_exhibition['updated_at_arr'] = $updated_at_arr_exhibition;
        $view_data_exhibition['image_arr'] = $image_arr_exhibition;
        $exhibitions_data = View::make('components.sitemap.sitemap_page', $view_data_exhibition)->render();
        $this->putSitemapToS3($sitemap_file_name_curation, $exhibitions_data);

        if (!empty($view_data_exhibition['urls_arr'])) {
            array_push($allSiteMap, $domainUrl . '/health_sitemap/' . $sitemap_file_name_curation);
        }

        //分類xml
        $sitemap_file_name_category = 'category_sitemap.xml';
        $categories = $this->getHealthCategories();

        $urls_arr_category = [];
        $updated_at_arr_category = [];
        foreach ($categories as $category) {
            $urls_arr_category[] = $domainUrl . '/' . $category['url'];
            $updated_at_arr_category[] = $category['updated_at'];
            // 子分類
            if ($category->subCategories->isNotEmpty()) {
                foreach ($category->subCategories as $subCategory) {
                    if (!empty($subCategory->en_name)) {
                        $urls_arr_category[] = $domainUrl . '/' . $category['url'] . '-' . $subCategory->en_name;
                        $updated_at_arr_category[] = $subCategory->updated_at;
                    }
                }
            }
        }
        $view_data_category['urls_arr'] = $urls_arr_category;
        $view_data_category['updated_at_arr'] = $updated_at_arr_category;
        $category_data = View::make('components.sitemap.sitemap_page', $view_data_category)->render();
        $this->putSitemapToS3($sitemap_file_name_category, $category_data);

        array_push($allSiteMap, $domainUrl . '/health_sitemap/' . $sitemap_file_name_category);

        //醫級專家xml
        $sitemap_file_name_experts = 'experts_sitemap.xml';
        $experts = $this->getHealthExperts();

        $urls_arr_experts = [];
        $updated_at_arr_experts = [];
        foreach ($experts as $expert) {
            $urls_arr_experts[] = $domainUrl . '/expert/' . $expert->en_name;
            $updated_at_arr_experts[] = $expert->updated_at;
        }
        $view_data_experts['urls_arr'] = $urls_arr_experts;
        $view_data_experts['updated_at_arr'] = $updated_at_arr_experts;
        $experts_data = View::make('components.sitemap.sitemap_page', $view_data_experts)->render();
        $this->putSitemapToS3($sitemap_file_name_experts, $experts_data);

        array_push($allSiteMap, $domainUrl . '/health_sitemap/' . $sitemap_file_name_experts);


        //count articles
        $countHealthArticles = $this->countHealthArticles();

        $countArticleDocument = 0;
        //文章xml
        if ($countHealthArticles > 0) {
            $size = 1000;
            $article_page_count = ceil($countHealthArticles / $size);

            for ($i = 1; $i <= $article_page_count; $i++) {
                $healthArticles = $this->getHealthArticlesForSitemap($size, $i);

                $urls_arr_article = [];
                $updated_at_arr_article = [];
                $image_arr_article = [];
                foreach ($healthArticles as $article) {
                    $urls_arr_article[] = $domainUrl . '/' . $article->mainCategories[0]->en_name . '/' . $article->articles_id;
                    $updated_at_arr_article[] = $article->updated_at;
                    $image_arr_article[] = $article->image;
                }
                $view_data_article['urls_arr'] = $urls_arr_article;
                $view_data_article['updated_at_arr'] = $updated_at_arr_article;
                $view_data_article['image_arr'] = $image_arr_article;
                $article_data = View::make('components.sitemap.sitemap_page', $view_data_article)->render();

                $sitemap_file_name_article = 'article_sitemap_' . $i . '.xml';

                $countArticleDocument = $i;

                $this->putSitemapToS3($sitemap_file_name_article, $article_data);
                array_push($allSiteMap, $domainUrl . '/health_sitemap/' . $sitemap_file_name_article);
            }
        }

        //main sitemap xml
        $sitemap_file_name_index = 'sitemap.xml';
        $view_data_index['sitemap_arr'] = $allSiteMap;
        $alldata = View::make('components.sitemap.sitemap_all_page', $view_data_index)->render();
        $this->putSitemapToS3($sitemap_file_name_index, $alldata);

        return Command::SUCCESS;
    }

    public function getHealthCuration()
    {
        $exhibitions = HealthExhibitionListModel::where('start_at', '<=', Carbon::now())
                        ->where('end_at', '>=', Carbon::now())
                        ->orderBy('start_at', 'desc')
                        ->get();

        return $exhibitions;
    }

    public function getHealthCategories()
    {
        $categories = MenuListModel::with(['subCategories' => function ($q) {
                            $q->where('status', 1)
                                ->orderBy('sort', 'asc');
        }])
                        ->where('menu_list_status', 1)
                        ->where('is_app', 0)
                        ->whereNotNull('categories_id')
                        ->get();

        return $categories;
    }

    public function getHealthExperts()
    {
        $experts = MastersModel::isDoctor()
                        ->active()
                        ->get();

        return $experts;
    }

    public function countHealthArticles()
    {
        $num = ArticleModel::active()->count();

        return $num;
    }

    public function getHealthArticlesForSitemap($size, $page = 1)
    {
        $articles = ArticleModel::active()
                        ->with('mainCategories')
                        ->offset(($page - 1) * $size)
                        ->limit($size)
                        ->get();

        return $articles;
    }

    public function countHealthArticlesOldCategories()
    {
        $num = ArticleModel::whereHas('mainCategories', function ($query) {
            $query->where('health_article_categories_mappings.sort', 0)
                    ->where('health_categories.created_at', '<', '2023-04-24 00:00:00');
        })->active()->count();

        return $num;
    }

    public function putSitemapToS3($data_name, $data)
    {
        $path = 'health2.0';
        if (!env('APP_WEB_URL') || stripos(env('APP_WEB_URL'), '-pre') || stripos(env('APP_WEB_URL'), 'local')) {
            $path .= '-pre';
        }
        $path .= '/health_sitemap/' . $data_name;

        Storage::disk('s3')->put($path, $data);
    }
}
