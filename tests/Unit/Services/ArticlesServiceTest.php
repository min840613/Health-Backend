<?php

namespace Tests\Unit\Services;

use App\Models\Articles\ArticleCategoriesMappingsModel;
use App\Models\Articles\ArticleModel;
use App\Models\Articles\KeyvisualModel;
use App\Models\Categories\MainCategoriesModel;
use App\Models\HomeArea\HomeTaxonModel;
use App\Repositories\ArticlesRepository;
use App\Repositories\KeyvisualRepository;
use App\Repositories\TaxonRepository;
use App\Services\ArticlesService;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Carbon\Carbon;
use Tests\TestCase;

class ArticlesServiceTest extends TestCase
{

    public function test_取得本日最新頭條_數量正確(): void
    {
        // arrange
        $count = 6;
        $except = [1, 2, 3];

        $expectedCount = $count;
        
        $this->mock(KeyvisualRepository::class)->shouldReceive('newsArticle')->once()->with($count, $except)->andReturn(new Collection([
            KeyvisualModel::factory()->published()->make(),
            KeyvisualModel::factory()->published()->make(),
            KeyvisualModel::factory()->published()->make(),
            KeyvisualModel::factory()->published()->make(),
            KeyvisualModel::factory()->published()->make(),
            KeyvisualModel::factory()->published()->make(),
        ]));

        // action
        $articles = app(ArticlesService::class)->newsArticle($count, $except);

        // assert
        $this->assertSame($articles->count(), $expectedCount);
    }

    public function test_取得本日最新頭條_數量缺少(): void
    {
        // arrange
        $count = 6;
        $except = [1, 2, 3];

        $expectedCount = $count;

        $keyVisuals = KeyvisualModel::factory()->published()->count(4)->make();

        $this->mock(KeyvisualRepository::class)->shouldReceive('newsArticle')->once()->andReturn(new Collection([
            $keyVisuals[0], $keyVisuals[1], $keyVisuals[2], $keyVisuals[3],
        ]));

        $articles = ArticleModel::factory()->published()->count(2)->make();

        $this->mock(ArticlesRepository::class)->shouldReceive('additional')->once()->andReturn(new Collection([
            $articles[0], $articles[1],
        ]));

        // action
        $articles = app(ArticlesService::class)->newsArticle($count, $except);

        // assert
        $this->assertSame($articles->count(), $expectedCount);
    }


    public function test_取得浮動區塊_數量正確(): void
    {
        $blockNum = 2;
        $count = 6;
        $expectArticleIds = []; // 只有 ArticlesService@additional會走到

        $expectedCount = $count;


        $homeTaxonModelMake = [
            'published_at' => now()->subDay()->toDateTimeString(),
            'published_end' =>  now()->addDay()->toDateTimeString(),
        ];

        $articleMake = [
            'publish' => now()->subDay()->toDateTimeString(),
            'adult_flag' => 0,
            'articles_status' => 1,
            'articles_id' => 1,
        ];

        $mainCategoryMake = ['categories_status' => 1];

        $homeTaxonModel = HomeTaxonModel::factory()->published()->make($homeTaxonModelMake)
        ->setRelation('categoryArticles', ArticleModel::factory()->count(7)->state(new Sequence(
            ['articles_id' => 1],
            ['articles_id' => 2],
            ['articles_id' => 3],
            ['articles_id' => 4],
            ['articles_id' => 5],
            ['articles_id' => 6],
            ['articles_id' => 7],
        ))->make())
        ->setRelation('article', ArticleModel::factory()->make($articleMake))
        ->setRelation('mainCategory', MainCategoriesModel::factory()->make($mainCategoryMake));
        

        $this->mock(TaxonRepository::class)->shouldReceive('block')->once()->with($blockNum, $count, $expectArticleIds)->andReturn($homeTaxonModel);

        
        $blockArticle = app(ArticlesService::class)->blockArticle($blockNum, $count, $expectArticleIds);

        $this->assertInstanceOf(HomeTaxonModel::class, $blockArticle['taxon']);
        $this->assertInstanceOf(ArticleModel::class, $blockArticle['headline']);
        $this->assertSame($blockArticle['articles']->count(), $expectedCount);

    }


    public function test_取得浮動區塊_數量缺少(): void
    {
        $blockNum = 2;
        $count = 6;
        $expectArticleIds = [3]; // 只有 ArticlesService@additional會走到

        $expectedCount = $count;


        $homeTaxonModelMake = [
            'published_at' => now()->subDay()->toDateTimeString(),
            'published_end' =>  now()->addDay()->toDateTimeString(),
        ];

        $articleMake = [
            'publish' => now()->subDay()->toDateTimeString(),
            'adult_flag' => 0,
            'articles_status' => 1,
            'articles_id' => 1,
        ];

        $mainCategoryMake = ['categories_status' => 1];

        $homeTaxonModel = HomeTaxonModel::factory()->published()->make($homeTaxonModelMake)
        ->setRelation('categoryArticles', ArticleModel::factory()->count(4)->state(new Sequence(
            ['articles_id' => 1],
            ['articles_id' => 2],
            ['articles_id' => 4],
        ))->make())
        ->setRelation('article', ArticleModel::factory()->make($articleMake))
        ->setRelation('mainCategory', MainCategoriesModel::factory()->make($mainCategoryMake));
        

        $this->mock(TaxonRepository::class)->shouldReceive('block')->once()->with($blockNum, $count, $expectArticleIds)->andReturn($homeTaxonModel);


        $articles = ArticleModel::factory()->published()->count(4)->state(new Sequence(
            ['articles_id' => 98],
            ['articles_id' => 99],
            ['articles_id' => 100],
            ['articles_id' => 101],
        ))->make();

        $this->mock(ArticlesRepository::class)->shouldReceive('additional')->once()->andReturn(new Collection([
            $articles[0], $articles[1], $articles[2], $articles[3],
        ]));
        
        $blockArticle = app(ArticlesService::class)->blockArticle($blockNum, $count, $expectArticleIds);

        $this->assertInstanceOf(HomeTaxonModel::class, $blockArticle['taxon']);
        $this->assertInstanceOf(ArticleModel::class, $blockArticle['headline']);
        $this->assertSame($blockArticle['articles']->count(), $expectedCount);

    }


    public function test_取得浮動區塊_沒有頭條(): void
    {
        $blockNum = 2;
        $count = 6;
        $expectArticleIds = []; // 只有 ArticlesService@additional會走到

        $expectedCount = $count;


        $homeTaxonModelMake = [
            'published_at' => now()->subDay()->toDateTimeString(),
            'published_end' =>  now()->addDay()->toDateTimeString(),
        ];

        $articleMake = [
            'publish' => now()->subDay()->toDateTimeString(),
            'adult_flag' => 0,
            'articles_status' => 1,
        ];
        $articleMake = [];

        $mainCategoryMake = ['categories_status' => 1];

        $homeTaxonModel = HomeTaxonModel::factory()->published()->make($homeTaxonModelMake)
        ->setRelation('categoryArticles', ArticleModel::factory()->count(2)->state(new Sequence(
            ['articles_id' => 2],
            ['articles_id' => 4],
        ))->make())
        ->setRelation('mainCategory', MainCategoriesModel::factory()->make($mainCategoryMake));

        $this->mock(TaxonRepository::class)->shouldReceive('block')->once()->with($blockNum, $count, $expectArticleIds)->andReturn($homeTaxonModel);


        $articles = ArticleModel::factory()->published()->count(4)->state(new Sequence(
            ['articles_id' => 98],
            ['articles_id' => 99],
            ['articles_id' => 100],
            ['articles_id' => 101],
        ))->make();

        $this->mock(ArticlesRepository::class)->shouldReceive('additional')->once()->andReturn(new Collection([
            $articles[0], $articles[1], $articles[2], $articles[3],
        ]));
        
        $blockArticle = app(ArticlesService::class)->blockArticle($blockNum, $count, $expectArticleIds);

        $this->assertInstanceOf(HomeTaxonModel::class, $blockArticle['taxon']);
        $this->assertNull($blockArticle['headline']);
        $this->assertSame($blockArticle['articles']->count(), $expectedCount);

    }
}
