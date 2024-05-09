<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HomeArea\MeasureModel;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ToolsApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_tools_有資料_取得到符合條件資料()
    {

        // 調用 factory
        $factory = MeasureModel::factory()->active()->sequence(fn (Sequence $sequence) => [
            'title' => 'A_'.$sequence->index, 
            'sort'  => $sequence->index
        ])->count(10)->create();

        // request api
        $response = $this->get(route('api.home.tools'));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'meta' => [
                'more_url' => null,
            ]
        ]);

        $data = $response->json()['data'];

        // 驗證型態
        $this->assertIsArray($data);

        // 驗證筆數
        $this->assertEquals(4, count($data));

        $verify_data = array_pop($data);

        // 驗證型態 (string)
        $this->assertIsString($verify_data['title']);

        // 驗證型態 (url)
        $this->assertEquals(true, filter_var($verify_data['image_url'], FILTER_VALIDATE_URL));
        $this->assertEquals(true, filter_var($verify_data['url'], FILTER_VALIDATE_URL));


        // 驗證 http status code
        $response->assertStatus(200);
    }


    public function test_tools_有資料_取不到符合條件資料()
    {

        // 調用 factory
        $factory = MeasureModel::factory()->active(0)->sequence(fn (Sequence $sequence) => [
            'title' => 'A_'.$sequence->index, 
            'sort'  => $sequence->index
        ])->count(10)->create();
        

        // request api
        $response = $this->get(route('api.home.tools'));


        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'meta' => [
                'more_url' => null,
            ],
            'data' => []
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }


    public function test_tools_沒資料_取不到符合條件資料()
    {

        // 調用 factory
        $factory = MeasureModel::factory()->active()->sequence(fn (Sequence $sequence) => [
            'title' => 'A_'.$sequence->index, 
            'sort'  => $sequence->index
        ])->count(0)->create();
        

        // request api
        $response = $this->get(route('api.home.tools'));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'meta' => [
                'more_url' => null,
            ],
            'data' => []
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }


}
