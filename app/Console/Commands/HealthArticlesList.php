<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Articles\ArticleModel;

class HealthArticlesList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'HealthArticle:ExportForDeepQ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '產生健康文章清單 For DeepQ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $headers = ['title', 'category', 'url', 'content', 'created_at', 'updated_at'];
        $fileName = 'tvbs_health.csv';
        $s3FileName = env('AWS_S3_DEEP_FOLDER', 'health2.0-pre') . '/ArticlesListForDeep/' . $fileName;

        // open CSV file
        $filePath = sys_get_temp_dir() . '/' . $fileName;
        $file = fopen($filePath, 'w');

        // add BOM to support UTF8
        fputs($file, "\xEF\xBB\xBF");
        fputcsv($file, $headers);

        ArticleModel::with('mainCategories')
                    ->select(['articles_id', 'title', 'video_id', 'article_content', 'created_at', 'updated_at'])
                    ->active()
                    ->chunk(5000, function ($articles) use ($file) {
                        foreach ($articles as $article) :
                            $content_url = 'content/health_' . $article->articles_id . '.xml';
                            $row = [
                                'title' => $article->title,
                                'category' => $article->mainCategories[0]['name'],
                                'url' => 'https://health.tvbs.com.tw/' . $article->mainCategories[0]['en_name'] . '/' . $article->articles_id,
                                'content' => $content_url,
                                'created_at' => $article->created_at,
                                'updated_at' => $article->updated_at
                            ];
                            fputcsv($file, $row);

                            // upload content to S3
                            $content_xml = '<video>' . $article->video_id . '</video>';
                            $content_xml .= $article->article_content;
                            $s3FileNameContent = env('AWS_S3_DEEP_FOLDER', 'health2.0-pre') . '/ArticlesListForDeep/content/health_' . $article->articles_id . '.xml';
                            Storage::disk('s3')->put($s3FileNameContent, $content_xml);
                        endforeach;
                    });
        // close file
        fclose($file);
        // upload CSV to S3
        Storage::disk('s3')->put($s3FileName, file_get_contents($filePath));
        // 刪除臨時文件
        unlink($filePath);

        return Command::SUCCESS;
    }
}
