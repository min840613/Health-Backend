<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Articles\ArticleCategoriesMappingsModel;
use App\Models\Articles\ArticleMasterMappingModel;
use App\Models\Articles\ArticleModel;
use App\Models\Categories\MainCategoriesModel;
use App\Models\Masters\DivisionsModel;
use App\Models\Masters\InstitutionsModel;
use App\Models\Masters\MasterDivisionModel;
use App\Models\Masters\MasterExperiencesModel;
use App\Models\Masters\MastersModel;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_取得醫級專家內容成功(): void
    {
        // arrange
        $institution = InstitutionsModel::factory()->published()->create();
        $divisions = DivisionsModel::factory()->published()->count(3)->create();

        $master = MastersModel::factory()->published()->has(MasterExperiencesModel::factory()->count(2)->state(new Sequence(
            ['is_current_job' => true],
            ['is_current_job' => false],
        )), 'experiences')->has(MasterDivisionModel::factory()->count(3)->state(new Sequence(
            ['division_id' => $divisions[0]->id],
            ['division_id' => $divisions[1]->id],
            ['division_id' => $divisions[2]->id],
        )), 'divisions')->hasExpertise(3)->create([
            'institution_id' => $institution->id,
            'is_contracted' => true,
        ]);

        $articles = ArticleModel::factory()->published()->notAdult()->count(4)->create();
        $mainCategory = MainCategoriesModel::factory()->filterAdvertorial()->create();
        $advertorialMainCategory = MainCategoriesModel::factory()->isAdvertorial()->create();

        ArticleMasterMappingModel::factory()->create(['article_id' => $articles[0]->articles_id, 'master_id' => $master->id]);

        ArticleCategoriesMappingsModel::factory()->count(4)->state(new Sequence(
            ['article_id' => $articles[0]->articles_id, 'category_id' => $mainCategory->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[1]->articles_id, 'category_id' => $mainCategory->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[2]->articles_id, 'category_id' => $advertorialMainCategory->categories_id, 'sort' => 0, 'parent' => null],
            ['article_id' => $articles[3]->articles_id, 'category_id' => $advertorialMainCategory->categories_id, 'sort' => 0, 'parent' => null],
        ))->create();

        // action
        $response = $this->get(route('api.master.show', ['expert_en_name' => $master->en_name]));

        // assert
        $response->assertOk();
        $response->assertJson([
            'data' => [
                'name' => $master->name,
                'image_url' => $master->content_image,
                'institution' => $master->institution->nick_name,
                'divisions' => [
                    [
                        'name' => $divisions[0]->name,
                        'description' => $master->divisions[0]->description
                    ],
                    [
                        'name' => $divisions[1]->name,
                        'description' => $master->divisions[1]->description
                    ],
                    [
                        'name' => $divisions[2]->name,
                        'description' => $master->divisions[2]->description
                    ],
                ],
                'title' => $master->title,
                'experiences' => [
                    [
                        'name' => $master->experiences[0]->name,
                        'is_current_job' => true,
                    ],
                    [
                        'name' => $master->experiences[1]->name,
                        'is_current_job' => false,
                    ],
                ],
                'expertise' => $master->expertise->pluck('name')->toArray(),
                'is_contracted' => true,
            ],
        ]);
        $response->assertJsonStructure([
            'data' => [
                'articles' => [
                    [
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
                    ],
                ],
            ],
        ]);
    }
}
