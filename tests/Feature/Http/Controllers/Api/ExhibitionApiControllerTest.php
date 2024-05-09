<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HealthExhibitionListModel;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ExhibitionApiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $connectionsToTransact = ['mysql_tvbs_2022'];

    public function test_topic_無參數_有資料_取得到符合條件資料()
    {
        // 調用 factory
        $factory = HealthExhibitionListModel::factory()->count(10)->create();

        // request api
        $response = $this->get(route('api.home.topic'));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'meta' => [
                'name' => '專題',
                'main_category' => '專題',
                'main_category_en' => 'topic'
            ]
        ]);

        $data = $response->json()['data'];

        // 驗證型態
        $this->assertIsArray($data);

        // 驗證筆數
        $this->assertEquals(app('App\Http\Controllers\Api\ExhibitionApiController')::TOPIC_COUNT, count($data));

        $verify_data = array_pop($data);
        
        // 驗證型態 (int)
        $this->assertIsInt($verify_data['exhibition_id']);

        // 驗證型態 (string)
        $this->assertIsString($verify_data['title']);

        // 驗證型態 (url)
        $this->assertEquals(true, filter_var($verify_data['image_url'], FILTER_VALIDATE_URL));
        $this->assertEquals(true, filter_var($verify_data['web_url'], FILTER_VALIDATE_URL));
        $this->assertEquals(true, filter_var($verify_data['app_url'], FILTER_VALIDATE_URL));

        // 驗證型態 (boolean)
        $this->assertIsBool($verify_data['is_blank']);

        // 驗證取得的資料是否符合 api 上寫的時間範圍內
        $start_at = false;
        if (strtotime($verify_data['start_at']) <= time()) {
            $start_at = true;
        } 

        $end_at = false;
        if (strtotime($verify_data['end_at']) > time()) {
            $end_at = true;
        } 

        $this->assertEquals(true, $start_at);
        $this->assertEquals(true, $end_at);
        $this->assertEquals('專題', $verify_data['main_category']);
        $this->assertEquals('topic', $verify_data['main_category_en']);

        // 驗證 http status code
        $response->assertStatus(200);
    }


    public function test_topic_無參數_有資料_取不到符合條件資料()
    {
        // 調用 factory
        $factory = HealthExhibitionListModel::factory()->expired()->count(10)->create();

        // request api
        $response = $this->get(route('api.home.topic'));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'data' => [],
            'meta' => [
                'name' => '專題',
                'main_category' => '專題',
                'main_category_en' => 'topic'
            ]
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }


    public function test_topic_無參數_無資料_取不到符合條件資料()
    {
        // 調用 factory
        $factory = HealthExhibitionListModel::factory()->expired()->count(0)->create();

        // request api
        $response = $this->get(route('api.home.topic'));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'data' => [],
            'meta' => [
                'name' => '專題',
                'main_category' => '專題',
                'main_category_en' => 'topic'
            ]
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }


    public function test_topic_有參數_有資料_取得到符合條件資料()
    {
        // 調用 factory
        $factory = HealthExhibitionListModel::factory()->count(10)->create();

        // api 參數, 預計取得五筆資料
        $count = 5;

        // request api
        $response = $this->get(route('api.home.topic', ['count' => $count]));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'meta' => [
                'name' => '專題',
                'main_category' => '專題',
                'main_category_en' => 'topic'
            ]
        ]);

        $data = $response->json()['data'];

        // 驗證型態
        $this->assertIsArray($data);

        // 驗證筆數
        $this->assertEquals($count, count($data));

        $verify_data = array_pop($data);
        
        // 驗證型態 (int)
        $this->assertIsInt($verify_data['exhibition_id']);

        // 驗證型態 (string)
        $this->assertIsString($verify_data['title']);

        // 驗證型態 (url)
        $this->assertEquals(true, filter_var($verify_data['image_url'], FILTER_VALIDATE_URL));
        $this->assertEquals(true, filter_var($verify_data['web_url'], FILTER_VALIDATE_URL));
        $this->assertEquals(true, filter_var($verify_data['app_url'], FILTER_VALIDATE_URL));

        // 驗證型態 (boolean)
        $this->assertIsBool($verify_data['is_blank']);

        // 驗證取得的資料是否符合 api 上寫的時間範圍內
        $start_at = false;
        if (strtotime($verify_data['start_at']) <= time()) {
            $start_at = true;
        } 

        $end_at = false;
        if (strtotime($verify_data['end_at']) > time()) {
            $end_at = true;
        } 

        $this->assertEquals(true, $start_at);
        $this->assertEquals(true, $end_at);
        $this->assertEquals('專題', $verify_data['main_category']);
        $this->assertEquals('topic', $verify_data['main_category_en']);

        // 驗證 http status code
        $response->assertStatus(200);
    }



    public function test_topic_有參數_無資料_取不到資料()
    {
        // 調用 factory
        $factory = HealthExhibitionListModel::factory()->count(0)->create();

        // api 參數, 預計取得五筆資料
        $count = 5;

        // request api
        $response = $this->get(route('api.home.topic', ['count' => $count]));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'meta' => [
                'name' => '專題',
                'main_category' => '專題',
                'main_category_en' => 'topic'
            ]
        ]);

        $data = $response->json()['data'];

        // 驗證型態
        $this->assertIsArray($data);

        // 驗證筆數
        $this->assertEquals(0, count($data));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'data' => [],
            'meta' => [
                'name' => '專題',
                'main_category' => '專題',
                'main_category_en' => 'topic'
            ]
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }


    public function test_topic_有參數_有資料_取不到符合條件資料()
    {
        // 調用 factory
        $factory = HealthExhibitionListModel::factory()->expired()->count(10)->create();

        // api 參數, 預計取得五筆資料
        $count = 5;

        // request api
        $response = $this->get(route('api.home.topic', ['count' => $count]));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'meta' => [
                'name' => '專題',
                'main_category' => '專題',
                'main_category_en' => 'topic'
            ]
        ]);

        $data = $response->json()['data'];

        // 驗證型態
        $this->assertIsArray($data);

        // 驗證筆數
        $this->assertEquals(0, count($data));

        // 驗證 response json 鍵值
        $response->assertJsonFragment([
            'status' => '00000',
            'message' => 'success',
            'data' => [],
            'meta' => [
                'name' => '專題',
                'main_category' => '專題',
                'main_category_en' => 'topic'
            ]
        ]);

        // 驗證 http status code
        $response->assertStatus(200);
    }

}
