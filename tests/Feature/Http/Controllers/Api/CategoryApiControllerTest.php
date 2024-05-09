<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Articles\ArticleCategoriesMappingsModel;
use App\Models\Articles\ArticleModel;
use App\Models\Articles\SponsorAdModel;
use App\Models\Categories\MainCategoriesModel;
use App\Models\Categories\MenuListModel;
use App\Models\Categories\SubCategoriesModel;
use App\Models\DailyViewCountModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_nav_有資料_取得到符合條件資料_無文章關聯(): void
    {
        // 調用 factory
        // 為了 sub categories 的外鍵，先產一個 main category
        $mainCategory = MainCategoriesModel::factory()->create();

        // 這邊做 hasMany 的關聯是因為在 CategoryApiNavResource 有使用到，但目前註解掉了，預先做好關聯性
        $factory = MenuListModel::factory()->recycle(SubCategoriesModel::factory()->count(3)->create(['categories_id' => $mainCategory->categories_id]))->sequence(fn(Sequence $sequence) => [
            'sort' => $sequence->index + 1,
            'menu_list_id' => $sequence->index + 1,
            'is_app' => 0,
            'categories_id' => $sequence->index + 1,
        ])->count(3)->create();

        // request api
        $response = $this->get(route('api.home.nav'));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
        ]);

        $data = $response->json('data');

        // 驗證型態
        $this->assertIsArray($data);

        // 驗證筆數
        $this->assertEquals(3, count($data));

        foreach ($data as $data_key => $verify_data) {
            // 驗證型態 (string)
            $this->assertIsString($verify_data['main_category']);

            // 驗證型態 (boolean)
            $this->assertIsBool(true, $verify_data['is_blank']);

            // 驗證型態 (url)
            if (is_null($verify_data['main_category_en'])) {
                $this->assertNull($verify_data['main_category_en']);
                $this->assertEquals(true, filter_var($verify_data['custom_url'], FILTER_VALIDATE_URL));
            } else {
                $this->assertNull($verify_data['custom_url']);
                $this->assertEquals(true, filter_var($verify_data['main_category_en'], FILTER_VALIDATE_URL));
            }
        }

        // 驗證 http status code
        $response->assertStatus(200);
    }

    public function test_nav_有資料_取不到符合條件資料(): void
    {
        // 調用 factory
        // 為了 sub categories 的外鍵，先產一個 main category
        $mainCategory = MainCategoriesModel::factory()->create();
        // dd(MenuListModel::all());
        // 這邊做 hasMany 的關聯是因為在 CategoryApiNavResource 有使用到，但目前註解掉了，預先做好關聯性
        $factory = MenuListModel::factory()->recycle(SubCategoriesModel::factory()->count(3)->create(['categories_id' => $mainCategory->categories_id]))->sequence(fn(Sequence $sequence) => [
            'sort' => $sequence->index + 1,
            'menu_list_id' => $sequence->index + 1,
            'is_app' => 1,
            'categories_id' => $sequence->index + 1,
        ])->count(3)->create();

        // request api
        $response = $this->get(route('api.home.nav'));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'data' => [],
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }

    public function test_nav_沒資料_取不到符合條件資料(): void
    {
        // 調用 factory
        // 為了 sub categories 的外鍵，先產一個 main category
        $mainCategory = MainCategoriesModel::factory()->create();

        // 這邊做 hasMany 的關聯是因為在 CategoryApiNavResource 有使用到，但目前註解掉了，預先做好關聯性
        $factory = MenuListModel::factory()->recycle(SubCategoriesModel::factory()->count(3)->create(['categories_id' => $mainCategory->categories_id]))->sequence(fn(Sequence $sequence) => [
            'sort' => $sequence->index + 1,
            'menu_list_id' => $sequence->index + 1,
            'is_app' => 1,
            'categories_id' => $sequence->index + 1,
        ])->count(0)->create();

        // request api
        $response = $this->get(route('api.home.nav'));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'data' => [],
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }

    public function test_導覽列取得子類別及有足夠的熱門文章(): void
    {
        // arrange
        Carbon::setTestNow('2023-01-01');

        $mainCategories = MainCategoriesModel::factory()->filterAdvertorial()->count(3)->hasSubCategories(4)->create();
        $menuLists = MenuListModel::factory()->count(3)->isWeb()->state(new Sequence(
            ['categories_id' => $mainCategories[0]->categories_id],
            ['categories_id' => $mainCategories[1]->categories_id],
            ['categories_id' => $mainCategories[2]->categories_id],
        ))->create();
        $articles = ArticleModel::factory()->count(12)->published()->create(['adult_flag' => 0]);
        ArticleCategoriesMappingsModel::factory()->count(12)->state(new Sequence(
            ['article_id' => $articles[0]->articles_id, 'category_id' => $mainCategories[0]->categories_id],
            ['article_id' => $articles[1]->articles_id, 'category_id' => $mainCategories[1]->categories_id],
            ['article_id' => $articles[2]->articles_id, 'category_id' => $mainCategories[2]->categories_id],
            ['article_id' => $articles[3]->articles_id, 'category_id' => $mainCategories[0]->categories_id],
            ['article_id' => $articles[4]->articles_id, 'category_id' => $mainCategories[1]->categories_id],
            ['article_id' => $articles[5]->articles_id, 'category_id' => $mainCategories[2]->categories_id],
            ['article_id' => $articles[6]->articles_id, 'category_id' => $mainCategories[0]->categories_id],
            ['article_id' => $articles[7]->articles_id, 'category_id' => $mainCategories[1]->categories_id],
            ['article_id' => $articles[8]->articles_id, 'category_id' => $mainCategories[2]->categories_id],
            ['article_id' => $articles[9]->articles_id, 'category_id' => $mainCategories[0]->categories_id],
            ['article_id' => $articles[10]->articles_id, 'category_id' => $mainCategories[1]->categories_id],
            ['article_id' => $articles[11]->articles_id, 'category_id' => $mainCategories[2]->categories_id],
        ))->create(['parent' => null, 'sort' => 0]);
        DailyViewCountModel::factory()->count(12)->state(new Sequence(
            ['source_id' => $articles[0]->articles_id],
            ['source_id' => $articles[1]->articles_id],
            ['source_id' => $articles[2]->articles_id],
            ['source_id' => $articles[3]->articles_id],
            ['source_id' => $articles[4]->articles_id],
            ['source_id' => $articles[5]->articles_id],
            ['source_id' => $articles[6]->articles_id],
            ['source_id' => $articles[7]->articles_id],
            ['source_id' => $articles[8]->articles_id],
            ['source_id' => $articles[9]->articles_id],
            ['source_id' => $articles[10]->articles_id],
            ['source_id' => $articles[11]->articles_id],
        ))->create(['date' => now()->toDateString()]);

        // action
        $response = $this->get(route('api.home.nav'));

        // assert
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                [
                    'main_category',
                    'main_category_en',
                    'sub_categories' => [
                        ['id', 'name'],
                    ],
                    'is_blank',
                    'custom_url',
                    'hot_articles' => [
                        ['article_id', 'main_category', 'main_category_en', 'image_url', 'is_video', 'title'],
                    ],
                ],
            ],
        ]);
    }

    public function test_取得類別首頁成功_僅有主類別(): void
    {
        // arrange
        Carbon::setTestNow('2023-01-01');
        $publishDate = Carbon::now();
        $mainCategories = MainCategoriesModel::factory()->filterAdvertorial()->published()->count(3)->hasSubCategories(4)->create();
        $mainCategoryAd = MainCategoriesModel::factory()->isAdvertorial()->published()->create();
        $articles = ArticleModel::factory()->count(14)->published()->notAdult()->create();
        foreach ($articles as $article) {
            $article->publish = $publishDate;
            $publishDate->subDay();
            $article->save();
        }
        ArticleCategoriesMappingsModel::factory()->count(2)->state(new Sequence(
            ['article_id' => $articles[0]->articles_id],
            ['article_id' => $articles[3]->articles_id],
        ))->create(['category_id' => $mainCategoryAd->categories_id, 'sort' => 0, 'parent' => null]);
        ArticleCategoriesMappingsModel::factory()->count(14)->state(new Sequence(
            ['article_id' => $articles[0]->articles_id, 'category_id' => $mainCategories[0]->categories_id, 'sort' => 1],
            ['article_id' => $articles[1]->articles_id, 'category_id' => $mainCategories[1]->categories_id, 'sort' => 0],
            ['article_id' => $articles[2]->articles_id, 'category_id' => $mainCategories[2]->categories_id, 'sort' => 0],
            ['article_id' => $articles[3]->articles_id, 'category_id' => $mainCategories[0]->categories_id, 'sort' => 1],
            ['article_id' => $articles[4]->articles_id, 'category_id' => $mainCategories[1]->categories_id, 'sort' => 0],
            ['article_id' => $articles[5]->articles_id, 'category_id' => $mainCategories[2]->categories_id, 'sort' => 0],
            ['article_id' => $articles[6]->articles_id, 'category_id' => $mainCategories[0]->categories_id, 'sort' => 0],
            ['article_id' => $articles[7]->articles_id, 'category_id' => $mainCategories[0]->categories_id, 'sort' => 0],
            ['article_id' => $articles[8]->articles_id, 'category_id' => $mainCategories[0]->categories_id, 'sort' => 0],
            ['article_id' => $articles[9]->articles_id, 'category_id' => $mainCategories[0]->categories_id, 'sort' => 0],
            ['article_id' => $articles[10]->articles_id, 'category_id' => $mainCategories[0]->categories_id, 'sort' => 0],
            ['article_id' => $articles[11]->articles_id, 'category_id' => $mainCategories[0]->categories_id, 'sort' => 0],
            ['article_id' => $articles[12]->articles_id, 'category_id' => $mainCategories[0]->categories_id, 'sort' => 0],
            ['article_id' => $articles[13]->articles_id, 'category_id' => $mainCategories[0]->categories_id, 'sort' => 0],
        ))->create(['parent' => null]);
        SponsorAdModel::factory()->count(2)->isMainCategory()->active()->state(new Sequence(
            ['article_id' => $articles[0]->articles_id, 'categories_list_id' => $mainCategories[0]->categories_id, 'position' => 3],
            ['article_id' => $articles[3]->articles_id, 'categories_list_id' => $mainCategories[0]->categories_id, 'position' => 8],
        ))->create();

        $request = [
            'main_category_en' => $mainCategories[0]->en_name,
        ];

        // action
        $response = $this->get(route('api.category.index', $request));

        // assert
        $response->assertOk();
        $response->assertJson([
            'data' => [
                ['article_id' => $articles[6]->articles_id],
                ['article_id' => $articles[7]->articles_id],
                ['article_id' => $articles[0]->articles_id], // 塞入廣編稿
                ['article_id' => $articles[8]->articles_id],
                ['article_id' => $articles[9]->articles_id],
                ['article_id' => $articles[10]->articles_id],
                ['article_id' => $articles[11]->articles_id],
                ['article_id' => $articles[3]->articles_id], // 塞入廣編稿
                ['article_id' => $articles[12]->articles_id],
                ['article_id' => $articles[13]->articles_id],
            ],
        ]);
    }
}
