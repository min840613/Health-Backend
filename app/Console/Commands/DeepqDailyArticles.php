<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\Articles\ArticleModel;
use App\Http\Resources\DeepqArticleCollection;

class DeepqDailyArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deepq:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Daily Articles to DeepQ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $new_articles = ArticleModel::with(['mainCategory' => function ($query) {
                                            $query->select('name', 'en_name');
        }])
                        ->select(['articles_id', 'title', 'video_id', 'article_content', 'created_at', 'updated_at'])
                        ->whereDate('publish', now()->addDay(-1))
                        ->get();

        foreach ($new_articles as $article) {
            $content_xml = '<video>' . $article->video_id . '</video>';
            $content_xml .= $article->article_content;
            $s3FileNameContent = config('deepq.aws.s3_folder') . '/ArticlesListForDeep/content/health_' . $article->articles_id . '.xml';
            Storage::disk('s3')->put($s3FileNameContent, $content_xml);
        }

        $mod_articles = ArticleModel::with(['mainCategory' => function ($query) {
                                            $query->select('name', 'en_name');
        }])
                        ->select(['articles_id', 'title', 'video_id', 'article_content', 'created_at', 'updated_at'])
                        ->whereDate('updated_at', now()->addDay(-1))
                        ->get();

        foreach ($mod_articles as $article) {
            $content_xml = '<video>' . $article->video_id . '</video>';
            $content_xml .= $article->article_content;
            $s3FileNameContent = config('deepq.aws.s3_folder') . '/ArticlesListForDeep/content/health_' . $article->articles_id . '.xml';
            Storage::disk('s3')->put($s3FileNameContent, $content_xml);
        }

        $del_articles = ArticleModel::with(['mainCategory' => function ($query) {
                                            $query->select('name', 'en_name');
        }])
                        ->select(['articles_id'])
                        ->whereDate('updated_at', now()->addDay(-1))
                        ->where('articles_status', 0)
                        ->get();

        $data = [];
        $data['add'] = DeepqArticleCollection::collection($new_articles);
        $data['revise'] = DeepqArticleCollection::collection($mod_articles);
        $data['delete'] = DeepqArticleCollection::collection($del_articles)->only('id');
        $data['source'] = 'health2.0';

        // dd(json_encode($data, JSON_PRETTY_PRINT));

        $response = Http::withToken(config('deepq.api.token'), 'Basic')->post(config('deepq.api.article_update_url'), $data);

        if ($response->failed()) {
            dd($response->json());
        }

        return Command::SUCCESS;
    }
}
