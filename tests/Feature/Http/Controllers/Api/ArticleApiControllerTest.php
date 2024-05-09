<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Articles\ArticleCategoriesMappingsModel;
use App\Models\Articles\ArticleModel;
use App\Models\Articles\KeyvisualModel;
use App\Models\Categories\MainCategoriesModel;
use App\Models\HomeArea\HomeTaxonModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ArticleApiControllerTest
 * @package Tests\Feature\Http\Controllers\Api
 */
class ArticleApiControllerTest extends TestCase
{
    use  RefreshDatabase, WithoutEvents;

    public function test_取得本日最新成功_頭條有達數量(): void
    {
        // arrange
        $count = 6;

        Carbon::setTestNow('2023-01-01');

        $categories = MainCategoriesModel::factory()->count(2)->create();
        $articles = ArticleModel::factory()->count($count)->published()->create([
            'publish' => now()->subDay()->toDateTimeString(),
            'adult_flag' => 0,
        ]);
        ArticleCategoriesMappingsModel::factory()->count($count)->state(new Sequence(
            ['article_id' => $articles[0]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[1]->articles_id, 'category_id' => $categories[1]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[2]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[3]->articles_id, 'category_id' => $categories[1]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[4]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[5]->articles_id, 'category_id' => $categories[1]->categories_id, 'sort' => 0, 'parent' => null],
        ))->create();
        KeyvisualModel::factory()->count($count)->published()->state(new Sequence(
            ['type' => $categories[0]->en_name, 'source_id' => $articles[0]->articles_id, 'sort' => 0],
            ['type' => $categories[1]->en_name, 'source_id' => $articles[1]->articles_id, 'sort' => 1],
            ['type' => null, 'source_id' => null, 'sort' => 2],
            ['type' => $categories[1]->en_name, 'source_id' => $articles[3]->articles_id, 'sort' => 3],
            ['type' => $categories[0]->en_name, 'source_id' => $articles[4]->articles_id, 'sort' => 4],
            ['type' => $categories[1]->en_name, 'source_id' => $articles[5]->articles_id, 'sort' => 5],
        ))->create();

        // action
        $response = $this->get(route('api.home.news', ['count' => $count]));

        // assert
        $response->assertOk();
        $response->assertJsonCount(6, 'data');
        $response->assertJsonStructure([
            'data' => [
                ['article_id', 'url', 'main_category', 'main_category_en', 'image_url', 'is_video', 'title'],
            ],
        ]);
    }

    public function test_取得本日最新成功_頭條未達數量_補上最新文章(): void
    {
        // arrange
        $count = 6;
        $unpublishedCount = 2;

        Carbon::setTestNow('2023-01-01');

        $categories = MainCategoriesModel::factory()->filterAdvertorial()->count(2)->create();
        $articles = ArticleModel::factory()->count($count)->published()->create([
            'adult_flag' => 0,
        ]);

        ArticleCategoriesMappingsModel::factory()->count($count)->state(new Sequence(
            ['article_id' => $articles[0]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[1]->articles_id, 'category_id' => $categories[1]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[2]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[3]->articles_id, 'category_id' => $categories[1]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[4]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[5]->articles_id, 'category_id' => $categories[1]->categories_id, 'sort' => 0, 'parent' => null],
        ))->create();
        KeyvisualModel::factory()->count($count - $unpublishedCount)->published()->state(new Sequence(
            ['type' => $categories[0]->en_name, 'source_id' => $articles[0]->articles_id, 'sort' => 0],
            ['type' => $categories[1]->en_name, 'source_id' => $articles[1]->articles_id, 'sort' => 1],
            ['type' => null, 'source_id' => null, 'sort' => 2],
            ['type' => $categories[1]->en_name, 'source_id' => $articles[3]->articles_id, 'sort' => 3],
        ))->create();
        KeyvisualModel::factory()->count($unpublishedCount)->unpublished()->state(new Sequence(
            ['type' => $categories[0]->en_name, 'source_id' => $articles[4]->articles_id, 'sort' => 4],
            ['type' => $categories[1]->en_name, 'source_id' => $articles[5]->articles_id, 'sort' => 5],
        ))->create();

        // action
        $response = $this->get(route('api.home.news', ['count' => $count]));

        // assert
        $response->assertOk();
        $response->assertJsonCount(6, 'data');
        $response->assertJsonStructure([
            'data' => [
                ['article_id', 'url', 'main_category', 'main_category_en', 'image_url', 'is_video', 'title'],
            ],
        ]);
    }

    public function test_取得浮動區塊_有達數量(): void
    {
        $count = 7;

        Carbon::setTestNow('2023-01-01');

        $categories = MainCategoriesModel::factory()->count(2)->filterAdvertorial()->create(['categories_status' => 1]);

        $articles = ArticleModel::factory()->count($count)->published()->create([
            'publish' => now()->subDay()->toDateTimeString(),
            'adult_flag' => 0,
            'articles_status' => 1,
        ]);

        ArticleCategoriesMappingsModel::factory()->count($count)->state(new Sequence(
            ['article_id' => $articles[0]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[1]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[2]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[3]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[4]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[5]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[6]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
        ))->create();

        HomeTaxonModel::factory()->count($count)->published()->create([
            'categories_id' => $categories[0]->categories_id,
            'article_id' => $articles[0]->articles_id,
            'published_at' => now()->subDay()->toDateTimeString(),
            'published_end' => now()->addDay()->toDateTimeString(),
        ]);

        $response = $this->get(route('api.home.block', ['block_num' => 2]));

        // assert
        $response->assertOk();

        $response->assertJsonCount(6, 'data');
        $response->assertJsonCount(4, 'meta');

        $response->assertJsonStructure([
            'data' => [
                ['article_id', 'image_url', 'main_category', 'main_category_en', 'sub_category', 'sub_category_id', 'is_video', 'title'],
            ],
            'meta' => [
                'headlines' => [
                    'article_id',
                    'image_url',
                    'main_category',
                    'main_category_en',
                    'sub_category',
                    'sub_category_id',
                    'is_video',
                    'title',
                    'content',
                    'tags',
                ]
                , 'name', 'main_category', 'main_category_en',
            ],
        ]);
    }

    public function test_取得浮動區塊_有達數量_去除未來稿(): void
    {
        $count = 7;

        Carbon::setTestNow('2023-01-01');

        $categories = MainCategoriesModel::factory()->count(2)->filterAdvertorial()->create(['categories_status' => 1]);

        // 未來稿
        $futureArticle = ArticleModel::factory()->create([
            'publish' => now()->addDay()->toDateTimeString(),
            'adult_flag' => 0,
            'articles_status' => 1,
        ]);

        $articles = ArticleModel::factory()->count($count)->published()->create([
            'publish' => now()->subDay()->toDateTimeString(),
            'adult_flag' => 0,
            'articles_status' => 1,
        ]);

        ArticleCategoriesMappingsModel::factory()->count($count + 1)->state(new Sequence(
            ['article_id' => $futureArticle->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[0]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[1]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[2]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[3]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[4]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[5]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[6]->articles_id, 'category_id' => $categories[0]->categories_id, 'sort' => 0, 'parent' => null],
        ))->create();

        HomeTaxonModel::factory()->count($count)->published()->create([
            'categories_id' => $categories[0]->categories_id,
            'article_id' => $articles[0]->articles_id,
            'published_at' => now()->subDay()->toDateTimeString(),
            'published_end' => now()->addDay()->toDateTimeString(),
        ]);

        $response = $this->get(route('api.home.block', ['block_num' => 2]));

        // assert
        $response->assertOk();

        $response->assertJsonCount(6, 'data');
        $response->assertJsonCount(4, 'meta');
        $response->assertJson([
            'meta' => [
                'headlines' => [
                    'article_id' => $articles[0]->articles_id,
                ],
            ],
        ]);
        $response->assertJsonStructure([
            'data' => [
                ['article_id', 'image_url', 'main_category', 'main_category_en', 'sub_category', 'sub_category_id', 'is_video', 'title'],
            ],
            'meta' => [
                'headlines' => [
                    'article_id',
                    'image_url',
                    'main_category',
                    'main_category_en',
                    'sub_category',
                    'sub_category_id',
                    'is_video',
                    'title',
                    'content',
                    'tags',
                ]
                , 'name', 'main_category', 'main_category_en',
            ],
        ]);
    }

    public function test_取得浮動區塊_未達數量_補上同分類文章(): void
    {
        $count = 10;

        Carbon::setTestNow('2023-01-01');

        $categories = MainCategoriesModel::factory()->count(2)->filterAdvertorial()->create(['categories_status' => 1]);

        $articles = ArticleModel::factory()->count($count)->published()->create([
            'publish' => now()->subDay()->toDateTimeString(),
            'adult_flag' => 0,
            'articles_status' => 1,
        ]);

        ArticleCategoriesMappingsModel::factory()->count($count)->state(new Sequence(
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

        HomeTaxonModel::factory()->count($count)->published()->create([
            'categories_id' => $categories[0]->categories_id,
            'article_id' => $articles[0]->articles_id,
            'published_at' => now()->subDay()->toDateTimeString(),
            'published_end' => now()->addDay()->toDateTimeString(),
        ]);

        $response = $this->get(route('api.home.block', ['block_num' => 2]));

        // assert
        $response->assertOk();

        $response->assertJsonCount(6, 'data');
        $response->assertJsonCount(4, 'meta');

        $response->assertJsonStructure([
            'data' => [
                ['article_id', 'image_url', 'main_category', 'main_category_en', 'sub_category', 'sub_category_id', 'is_video', 'title'],
            ],
            'meta' => [
                'headlines' => [
                    'article_id',
                    'image_url',
                    'main_category',
                    'main_category_en',
                    'sub_category',
                    'sub_category_id',
                    'is_video',
                    'title',
                    'content',
                    'tags',
                ]
                , 'name', 'main_category', 'main_category_en',
            ],
        ]);
    }


    public function test_取得浮動區塊_未達數量_補上同分類文章_沒有頭條(): void
    {
        $count = 10;

        Carbon::setTestNow('2023-01-01');

        $categories = MainCategoriesModel::factory()->filterAdvertorial()->count(2)->create(['categories_status' => 1]);

        $articles = ArticleModel::factory()->count($count)->published()->create([
            'adult_flag' => 0,
        ]);

        ArticleCategoriesMappingsModel::factory()->count($count)->state(new Sequence(
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

        HomeTaxonModel::factory()->count(3)->state(new Sequence(
            ['sort' => 0, 'article_id' => $articles[0]->articles_id],
            ['sort' => 1, 'article_id' => $articles[1]->articles_id],
            ['sort' => 2, 'article_id' => $articles[2]->articles_id],
        ))->published()->create([
            'categories_id' => $categories[0]->categories_id,
            'published_at' => now()->subDay()->toDateTimeString(),
            'published_end' => now()->addDay()->toDateTimeString(),
        ]);

        $response = $this->get(route('api.home.block', ['block_num' => 2]));

        // assert
        $response->assertOk();

        $response->assertJsonCount(6, 'data');
        $response->assertJsonCount(4, 'meta');

        $response->assertJsonStructure([
            'data' => [
                ['article_id', 'image_url', 'main_category', 'main_category_en', 'sub_category', 'sub_category_id', 'is_video', 'title'],
            ],
            'meta' => [
                'headlines', 'name', 'main_category', 'main_category_en',
            ],
        ]);
    }
}
