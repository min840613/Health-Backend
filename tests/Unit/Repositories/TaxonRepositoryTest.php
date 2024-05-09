<?php

namespace Tests\Unit\Repositories;

use App\Models\Articles\ArticleCategoriesMappingsModel;
use App\Models\Articles\ArticleModel;
use App\Models\Categories\MainCategoriesModel;
use App\Models\HomeArea\HomeTaxonModel;
use App\Repositories\TaxonRepository;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class TaxonRepositoryTest
 * @package Tests\Unit\Repositories
 */
class TaxonRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TaxonRepository $repository;

    protected function setUp() :void
    {
        parent::setUp();
        $this->repository = new TaxonRepository(new HomeTaxonModel());
    }

    /**
     * @group repository
     */
    public function test_block() :void
    {
        $categories = MainCategoriesModel::factory()->count(2)->create(['categories_status' => 1]);

        $articles = ArticleModel::factory()->count(10)->published()->create([
            'publish' => now()->subDay()->toDateTimeString(),
            'adult_flag' => 0,
            'articles_status' => 1,
        ]);

        ArticleCategoriesMappingsModel::factory()->count(10)->state(new Sequence(
            ['article_id' => $articles[0]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[1]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[2]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[3]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[4]->articles_id, 'category_id' => $categories[1]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[5]->articles_id, 'category_id' => $categories[1]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[6]->articles_id, 'category_id' => $categories[1]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[7]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[8]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[9]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
        ))->create();

        HomeTaxonModel::factory()->count(3)->published()->create([
            'categories_id' => $categories[0]->categories_id,
            'article_id' => $articles[0]->articles_id,
            'published_at' => now()->subDay()->toDateTimeString(),
            'published_end' =>  now()->addDay()->toDateTimeString(),
            'status' => 1
        ]);


        $blockNum = 2;
        $count = 5;
        $expectArticleIds = [3];

        $block = $this->repository->block($blockNum, $count, $expectArticleIds);
        $this->assertInstanceOf(HomeTaxonModel::class, $block);
        $this->assertSame($count, $block->categoryArticles->count());
        $this->assertSame($articles[0]->articles_id, $block->article->articles_id);
        $this->assertSame($categories[0]->categories_id, $block->mainCategory->categories_id);
    }

}
