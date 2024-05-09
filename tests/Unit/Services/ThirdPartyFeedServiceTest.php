<?php

namespace Tests\Unit\Services;

use App\Models\Articles\ArticleModel;
use App\Models\Categories\MainCategoriesModel;
use App\Models\ThirdPartyFeed\MixerboxArticleConditionModel;
use App\Models\ThirdPartyFeed\MixerboxArticlesModel;
use App\Repositories\MixerboxArticleConditionsRepository;
use App\Repositories\MixerboxArticlesRepository;
use App\Services\ThirdPartyFeedService;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ThirdPartyFeedServiceTest extends TestCase
{
    public function test_取得MixerboxRss成功_滿足數量的rss文章(): void
    {
        // arrange
        Carbon::setTestNow('2023-01-01');
        $total = 6;
        $perLimit = 1;

        $categories = MainCategoriesModel::factory()->count($total)->make();

        $articles = collect();
        for ($i = 0; $i < $total; $i++) {
            $articles[$i] = ArticleModel::factory()->published()->notAdult()->make(['is_mixerbox_article' => 1]);
            $articles[$i]->mainCategory = $categories[$i];
        }

        $mixerbox = collect();
        for ($i = 0; $i < $total; $i++) {
            $mixerbox[$i] = MixerboxArticlesModel::factory()->make(['release_date' => now()->toDateString()]);
            $mixerbox[$i]->article = $articles[$i];
        }

        $this->mock(MixerboxArticlesRepository::class)->shouldReceive('rss')->andReturn($mixerbox);
        $this->mock(MixerboxArticleConditionsRepository::class)->shouldReceive('all')->never();
        $this->mock(MixerboxArticleConditionsRepository::class)->shouldReceive('release')->never();

        // action
        $actual = app(ThirdPartyFeedService::class)->mixerboxRss(now(), $total, $perLimit);

        // assert
        $this->assertSame(6, $actual->count());
    }

    public function test_取得MixerboxRss成功_未滿足數量的rss文章(): void
    {
        // arrange
        Carbon::setTestNow('2023-01-01');
        $total = 6;
        $perLimit = 1;

        $categories = MainCategoriesModel::factory()->count($total)->make();

        $articles = collect();
        for ($i = 0; $i < $total; $i++) {
            $articles[$i] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => $i + 1, 'is_mixerbox_article' => 1]);
            $articles[$i]->mainCategory = $categories[$i];
        }

        $conditions = MixerboxArticleConditionModel::factory()->count($total)->state(new Sequence(
            ['category_id' => $categories[0]->categories_id],
            ['category_id' => $categories[1]->categories_id],
            ['category_id' => $categories[2]->categories_id],
            ['category_id' => $categories[3]->categories_id],
            ['category_id' => $categories[4]->categories_id],
            ['category_id' => $categories[5]->categories_id],
        ))->make();

        $mixerbox = collect();
        // 假設有六篇，但僅有四篇今日已供稿，兩篇新加入的
        for ($i = 0; $i < $total; $i++) {
            $releaseDate = $i >= 4 ? null : now()->toDateString();
            $mixerbox[$i] = MixerboxArticlesModel::factory()->make(['release_date' => $releaseDate, 'created_at' => now()->addDays($i)]);
            $mixerbox[$i]->article = $articles[$i];
        }

        $this->mock(MixerboxArticlesRepository::class, function ($mock) use ($mixerbox) {
            $mock->shouldReceive('rss')->once()->andReturn($mixerbox);
            $mock->shouldReceive('release')->once();
        });

        $this->mock(MixerboxArticleConditionsRepository::class)->shouldReceive('all')->once()->andReturn($conditions);

        // action
        $actual = app(ThirdPartyFeedService::class)->mixerboxRss(now(), $total, $perLimit);

        // assert
        $this->assertSame(6, $actual->count());
        $this->assertSame([6, 5, 4, 3, 2, 1], $actual->pluck('articles_id')->toArray());
    }

    public function test_取得MixerboxRss成功_各類別上限2筆成功_滿足rss文章(): void
    {
        // arrange
        $total = 6;
        $perLimit = 2;

        $categories = MainCategoriesModel::factory()->count($total)->make();

        $articles[0] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 1, 'is_mixerbox_article' => 1]);
        $articles[0]->mainCategory = $categories[0];
        $articles[1] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 2, 'is_mixerbox_article' => 1]);
        $articles[1]->mainCategory = $categories[1];
        $articles[2] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 3, 'is_mixerbox_article' => 1]);
        $articles[2]->mainCategory = $categories[0];
        $articles[3] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 4, 'is_mixerbox_article' => 1]);
        $articles[3]->mainCategory = $categories[1];
        $articles[4] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 5, 'is_mixerbox_article' => 1]);
        $articles[4]->mainCategory = $categories[2];
        $articles[5] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 6, 'is_mixerbox_article' => 1]);
        $articles[5]->mainCategory = $categories[2];

        $mixerbox = collect();
        for ($i = 0; $i < $total; $i++) {
            $mixerbox[$i] = MixerboxArticlesModel::factory()->make(['release_date' => now()->toDateString()]);
            $mixerbox[$i]->article = $articles[$i];
        }

        $this->mock(MixerboxArticlesRepository::class)->shouldReceive('rss')->andReturn($mixerbox);
        $this->mock(MixerboxArticleConditionsRepository::class)->shouldReceive('all')->never();
        $this->mock(MixerboxArticleConditionsRepository::class)->shouldReceive('release')->never();

        // action
        $actual = app(ThirdPartyFeedService::class)->mixerboxRss(now(), $total, $perLimit);

        // assert
        $this->assertSame(6, $actual->count());
    }

    public function test_取得MixerboxRss成功_各類別上限2筆成功_未滿足數量的rss文章(): void
    {
        // arrange
        Carbon::setTestNow('2023-01-01');
        $total = 6;
        $perLimit = 2;

        $categories = MainCategoriesModel::factory()->count($total)->make();

        $articles[0] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 1, 'is_mixerbox_article' => 1]);
        $articles[0]->mainCategory = $categories[0];
        $articles[1] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 2, 'is_mixerbox_article' => 1]);
        $articles[1]->mainCategory = $categories[1];
        $articles[2] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 3, 'is_mixerbox_article' => 1]);
        $articles[2]->mainCategory = $categories[2];
        $articles[3] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 4, 'is_mixerbox_article' => 1]);
        $articles[3]->mainCategory = $categories[1];
        $articles[4] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 5, 'is_mixerbox_article' => 1]);
        $articles[4]->mainCategory = $categories[1];
        $articles[5] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 6, 'is_mixerbox_article' => 1]);
        $articles[5]->mainCategory = $categories[1];
        $articles[6] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 7, 'is_mixerbox_article' => 1]);
        $articles[6]->mainCategory = $categories[4];
        $articles[7] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 8, 'is_mixerbox_article' => 1]);
        $articles[7]->mainCategory = $categories[1];
        $articles[8] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 9, 'is_mixerbox_article' => 1]);
        $articles[8]->mainCategory = $categories[2];
        $articles[9] = ArticleModel::factory()->published()->notAdult()->make(['articles_id' => 10, 'is_mixerbox_article' => 1]);
        $articles[9]->mainCategory = $categories[4];

        $conditions = MixerboxArticleConditionModel::factory()->count($total)->state(new Sequence(
            ['category_id' => $categories[0]->categories_id],
            ['category_id' => $categories[1]->categories_id],
            ['category_id' => $categories[2]->categories_id],
            ['category_id' => $categories[3]->categories_id],
            ['category_id' => $categories[4]->categories_id],
            ['category_id' => $categories[5]->categories_id],
        ))->make();

        $mixerbox = collect();
        // 假設有六篇，但僅有三篇今日已供稿，兩篇新加入的
        for ($i = 0; $i < 10; $i++) {
            $releaseDate = $i >= 3 ? null : now()->toDateString();
            $mixerbox[$i] = MixerboxArticlesModel::factory()->make(['release_date' => $releaseDate, 'created_at' => now()->addDays($i)]);
            $mixerbox[$i]->article = $articles[$i];
        }

        $this->mock(MixerboxArticlesRepository::class, function ($mock) use ($mixerbox) {
            $mock->shouldReceive('rss')->once()->andReturn($mixerbox);
            $mock->shouldReceive('release')->once();
        });

        $this->mock(MixerboxArticleConditionsRepository::class)->shouldReceive('all')->once()->andReturn($conditions);

        // action
        $actual = app(ThirdPartyFeedService::class)->mixerboxRss(now(), $total, $perLimit);

        // assert
        $this->assertSame(6, $actual->count());
        $this->assertSame([9, 7, 4, 3, 2, 1], $actual->pluck('articles_id')->toArray());
    }
}
