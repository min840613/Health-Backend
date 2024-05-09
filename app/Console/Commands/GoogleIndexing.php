<?php

namespace App\Console\Commands;

use App\Exceptions\GoogleIndexingException;
use App\Helpers\UrlHelper;
use App\Models\Articles\ArticleModel;
use Illuminate\Console\Command;

class GoogleIndexing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:google_indexing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '執行 google indexing 讓網站擁有者在新增或移除網頁時直接通知 Google，以便 Google 為這些網頁安排重新檢索，進一步提升使用者流量的品質。';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start = now()->subMinutes(30)->startOfMinute()->toDateTimeString();
        $end = now()->toDateTimeString();

        $articles = ArticleModel::with(['mainCategory:en_name'])
            ->active()
            ->whereBetween('publish', [$start, $end])
            ->get(['articles_id']);

        try {
            $articles->each(function (ArticleModel $article) {
                $url = UrlHelper::generateWebUrl($article->articles_id, $article->mainCategory->en_name);
                \Indexing::update($url);
            });
        } catch (GoogleIndexingException $exception) {
            \Log::alert($exception);
        }
    }
}
