<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\KeywordClickCountModel;
use Illuminate\Database\Eloquent\Factories\Sequence;

class KeywordApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('s3');
    }

    public function test_index_無參數_有資料_取得到符合條件資料()
    {
        // 調用 factory
        $factory = KeywordClickCountModel::factory()->count(10)->state(new Sequence(
                ['keyword' => 'A1', 'click_count' => 1111, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A2', 'click_count' => 1112],
                ['keyword' => 'A3', 'click_count' => 1113],
                ['keyword' => 'A10', 'click_count' => 1114],
                ['keyword' => 'A5', 'click_count' => 1115],
                ['keyword' => 'A6', 'click_count' => 1116],
                ['keyword' => 'A7', 'click_count' => 1117],
                ['keyword' => 'A8', 'click_count' => 1118],
                ['keyword' => 'A9', 'click_count' => 1119],
                ['keyword' => 'A4', 'click_count' => 1120],
        ))->create();

        // request api
        $response = $this->get(route('api.home.keywords'));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'data' => ['A8', 'A2', 'A3', 'A10', 'A5', 'A6', 'A7'] // 順序不分
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }


    public function test_index_無參數_有資料_取不到符合條件資料()
    {
        // 調用 factory
        $factory = KeywordClickCountModel::factory()->count(10)->state(new Sequence(
                ['keyword' => 'A1', 'click_count' => 1111, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A2', 'click_count' => 1112, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A3', 'click_count' => 1113, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A10', 'click_count' => 1114, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A5', 'click_count' => 1115, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A6', 'click_count' => 1116, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A7', 'click_count' => 1117, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A8', 'click_count' => 1118, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A9', 'click_count' => 1119, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A4', 'click_count' => 1120, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
        ))->create();

        // request api
        $response = $this->get(route('api.home.keywords'));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'data' => []
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }

    public function test_index_無參數_無資料_取不到符合條件資料()
    {
        // 調用 factory
        $factory = KeywordClickCountModel::factory()->count(0)->create();

        // request api
        $response = $this->get(route('api.home.keywords'));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'data' => []
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }

    public function test_index_有參數_有資料_取得到符合條件資料()
    {
        // 調用 factory
        $factory = KeywordClickCountModel::factory()->count(10)->state(new Sequence(
                ['keyword' => 'A1', 'click_count' => 1111],
                ['keyword' => 'A2', 'click_count' => 1112],
                ['keyword' => 'A3', 'click_count' => 1113],
                ['keyword' => 'A10', 'click_count' => 1114],
                ['keyword' => 'A5', 'click_count' => 1115],
                ['keyword' => 'A6', 'click_count' => 1116],
                ['keyword' => 'A7', 'click_count' => 1117],
                ['keyword' => 'A8', 'click_count' => 1118],
                ['keyword' => 'A9', 'click_count' => 1119],
                ['keyword' => 'A4', 'click_count' => 1120],
        ))->create();

        // request api
        $response = $this->call('GET', route('api.home.keywords'), ['count' => 2]);

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'data' => ['A1', 'A2'] // 順序不分
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }

    public function test_index_有參數_有資料_取不到符合條件資料()
    {
        // 調用 factory
        $factory = KeywordClickCountModel::factory()->count(10)->state(new Sequence(
                ['keyword' => 'A1', 'click_count' => 1111, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A2', 'click_count' => 1112, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A3', 'click_count' => 1113, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A10', 'click_count' => 1114, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A5', 'click_count' => 1115, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A6', 'click_count' => 1116, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A7', 'click_count' => 1117, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A8', 'click_count' => 1118, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A9', 'click_count' => 1119, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
                ['keyword' => 'A4', 'click_count' => 1120, 'date' => now()->subDay(5)->toDateTimeString()], // 設立五天前不符條件
        ))->create();

        // request api
        $response = $this->call('GET', route('api.home.keywords'), ['count' => 2]);

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'data' => []
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }

    public function test_index_有參數_無資料_取不到符合條件資料()
    {
        // 調用 factory
        $factory = KeywordClickCountModel::factory()->count(0)->create();

        // request api
        $response = $this->call('GET', route('api.home.keywords'), ['count' => 2]);

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'data' => []
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }

}
