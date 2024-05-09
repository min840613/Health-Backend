<?php

namespace App\Console\Commands;

use App\Models\Articles\ArticleModel;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ArticleMasterMappingTransform extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trans:article_master_mapping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '因文章可以選多個醫師，將 health_articles 的 talent_category_id 轉換到新表';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ArticleModel::query()->where('talent_category_id', '>', 0)->chunk(500, function (Collection $articles) {
            foreach ($articles as $article) {
                $article->masters()->attach($article->talent_category_id);
            }
        });
    }
}
