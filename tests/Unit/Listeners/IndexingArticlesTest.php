<?php

namespace Tests\Unit\Listeners;

use App\Events\ArticleStored;
use App\Events\ArticleUpdated;
use App\Listeners\IndexingArticles;
use App\Models\Articles\ArticleModel;
use App\Models\Categories\MainCategoriesModel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexingArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function getArticleCreateSucceed(): array
    {
        Carbon::setTestNow('2023-01-01');
        return [
            '文章上架且發布時間已過' => [
                'article' => [
                    'articles_status' => 1,
                    'publish' => now()->subDay()->toDateTimeString(),
                ],
                'expected' => 1,
            ],
            '文章下架' => [
                'article' => [
                    'articles_status' => 0,
                    'publish' => now()->toDateTimeString(),
                ],
                'expected' => 0,
            ],
        ];
    }

    /**
     * @dataProvider getArticleCreateSucceed
     * @param array $articleData
     * @param int $expected
     * @return void
     * @throws \Exception
     */
    public function test_文章新增成功(array $articleData, int $expected): void
    {
        // arrange
        \Indexing::spy();

        $article = ArticleModel::factory()->create($articleData);
        $article->setRelation('mainCategory', MainCategoriesModel::factory()->make(['en_name' => 'testing']));
        $event = new ArticleStored($article);

        // action
        $listener = app(IndexingArticles::class);
        $listener->handle($event);

        // assert
        if ($expected === 0) {
            \Indexing::shouldNotHaveReceived('create');
        } else {
            \Indexing::shouldHaveReceived('create')->times($expected);
        }
    }

    public function getArticleUpdateSucceed(): array
    {
        return [
            '文章下架' => [
                'old_article' => [
                    'articles_status' => 1,
                ],
                'new_article' => [
                    'articles_status' => 0,
                ],
                'expected' => [
                    'method' => 'delete',
                    'times' => 1,
                ],
            ],
            '文章狀態更新且上架及發布時間已過' => [
                'old_article' => [
                    'articles_status' => 0,
                ],
                'new_article' => [
                    'articles_status' => 1,
                    'publish' => now()->subDay()->toDateTimeString(),
                ],
                'expected' => [
                    'method' => 'update',
                    'times' => 1,
                ],
            ],
            '文章資料更新且上架及發布時間已過' => [
                'old_article' => [
                    'articles_status' => 1,
                    'title' => 'test',
                ],
                'new_article' => [
                    'articles_status' => 1,
                    'publish' => now()->subDay()->toDateTimeString(),
                ],
                'expected' => [
                    'method' => 'update',
                    'times' => 1,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getArticleUpdateSucceed
     * @param array $oldArticle
     * @param array $newArticle
     * @param array $expected
     * @return void
     * @throws \Exception
     */
    public function test_文章更新成功(array $oldArticle, array $newArticle, array $expected): void
    {
        // arrange
        \Indexing::spy();

        $article = ArticleModel::factory()->create($oldArticle);
        $article->setRelation('mainCategory', MainCategoriesModel::factory()->make(['en_name' => 'testing']));

        $article->update($newArticle);

        $event = new ArticleUpdated($article);

        // action
        $listener = app(IndexingArticles::class);
        $listener->handle($event);

        // assert
        \Indexing::shouldHaveReceived($expected['method'])->times($expected['times']);
    }
}
