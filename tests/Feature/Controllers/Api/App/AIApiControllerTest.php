<?php

namespace Feature\Controllers\Api\App;

use App\Models\Deepq\KeywordModel;
use App\Models\Deepq\QuestionModel;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AIApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_AI小助理生成節氣關鍵字成功_無資料(): void
    {
        // arrange
        $request = [];

        // action
        $response = $this->get(route('api.app.ai.prompt', $request));

        // assert
        $response->assertOk();
        $response->assertJson([
            'data' => [
                'questions' => [],
            ],
        ]);
    }

    public function test_AI小助理生成節氣關鍵字成功_有資料且在區間內(): void
    {
        // arrange
        Carbon::setTestNow('2023-01-01');

        KeywordModel::factory()->count(3)->state(new Sequence(
            ['end_at' => now()->subDay()->toDateTimeString()],
            ['keyword' => 'fake keyword', 'count' => 3, 'start_at' => now()->toDateTimeString(), 'end_at' => now()->addDay()->toDateTimeString()],
        ))->has(QuestionModel::factory()->count(3)->state(new Sequence(
            ['question' => 'test2', 'sort' => 2],
            ['question' => 'test1', 'sort' => 1],
            ['question' => 'test3', 'sort' => 3],
        )), 'questions')->create();


        KeywordModel::factory()->count(1)->state(new Sequence(
            ['keyword' => 'fake keyword', 'count' => 1, 'start_at' => now()->subDay()->toDateTimeString(), 'end_at' => now()->toDateTimeString()],
        ))->has(QuestionModel::factory()->count(3)->state(new Sequence(
            ['question' => 'test-2', 'sort' => 2],
            ['question' => 'test-1', 'sort' => 1],
            ['question' => 'test-3', 'sort' => 3],
        )), 'questions')->create();

        $request = [];

        // action
        $response = $this->get(route('api.app.ai.prompt', $request));

        // assert
        $response->assertOk();
        $response->assertJson([
            'data' => [
                'questions' => ['test-1', 'test-2', 'test-3', 'test1', 'test2', 'test3'],
            ],
        ]);
    }

    public function getAIPromptKeywordSuccessData(): array
    {
        Carbon::setTestNow('2023-01-01');
        return [
            '無資料' => [
                'keywords' => null,
                'questions' => null,
                'expected' => [
                    'data' => [
                        'questions' => [],
                    ],
                ],
            ],
            '有資料且在區間內' => [
                'keywords' => [
                    ['end_at' => now()->subDay()->toDateTimeString()],
                    ['keyword' => 'fake keyword', 'count' => 3, 'end_at' => now()->toDateTimeString()],
                ],
                'questions' => [
                    'count' => 3,
                    'data' => [
                        ['question' => 'test2', 'sort' => 2],
                        ['question' => 'test1', 'sort' => 1],
                        ['question' => 'test3', 'sort' => 3],
                    ],
                ],
                'expected' => [
                    'data' => [
                        'questions' => ['test1', 'test2', 'test3'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getAIPromptKeywordSuccessData
     */
    public function test_AI小助理生成節氣關鍵字成功(?array $keywords, ?array $questions, array $expected): void
    {
        // arrange
        Carbon::setTestNow('2023-01-01');

        if ($keywords !== null) {
            if ($questions !== null) {
                KeywordModel::factory()->count(2)->state(new Sequence(...$keywords))
                    ->has(QuestionModel::factory()->count($questions['count'])->state(new Sequence(...$questions['data'])), 'questions')
                    ->create();
            } else {
                KeywordModel::factory()->count(2)->state(new Sequence(...$keywords))->create();
            }
        }

        $request = [];

        // action
        $response = $this->get(route('api.app.ai.prompt', $request));

        // assert
        $response->assertOk();
        $response->assertJson($expected);
    }
}
