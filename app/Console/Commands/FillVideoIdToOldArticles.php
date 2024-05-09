<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Articles\ArticleModel;

class FillVideoIdToOldArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videoId:fill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill Video Id To Old Articles';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $documentPath = public_path() . "/documents/" . "OldArticles_ViedoId.csv";

        try {
            $csvData = [];
            if (($open = fopen($documentPath, "r")) !== false) {
                while (($data = fgetcsv($open, 5000, ",")) !== false) {
                    $csvData[] = $data;
                }
                fclose($open);
            }

            $count = 0;
            foreach ($csvData as $key => $val) {
                $update = [];

                if ($key > 0) {
                    $csvForArticles = explode('/', $val[0]);

                    if ($csvForArticles[3] == 'exhibition') {
                        echo "Article ID： " . $csvForArticles[4] . "    為策展，跳過 \n";
                        continue;
                    }

                    $csvForVideos = explode('/', $val[1]);

                    $videoId = explode('?', $csvForVideos[4])[0];

                    $articleId = $csvForArticles[4];

                    $update = [
                        'video_id' => $videoId,
                        'updated_user' => auth()->user()->name ?? 'dev001'
                    ];

                    $article = ArticleModel::find($articleId);
                    if (!$article) {
                        echo "Article ID： " . $articleId . "    找不到 \n";
                        continue;
                    }

                    $article->update($update);
                    echo "Article ID： " . $articleId . "  ，  Video ID： " . $videoId . "  匯入成功 \n";
                    $count++;
                }
            }

            echo "\n\n 共完成：" . $count . " 筆，請檢查筆數是否正確 \n\n";
            return Command::SUCCESS;
        } catch (Exception $e) {
            echo '錯誤: ' . $e->getMessage() . "\n";
            return Command::FAILURE;
        }
    }
}
