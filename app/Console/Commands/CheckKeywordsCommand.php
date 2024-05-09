<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Aws\CloudFront\CloudFrontClient;
use Carbon\Carbon;
use App\Models\KeywordClickCountModel;

class CheckKeywordsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Ai:CheckRestrictedKeywords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '透過 Ai 確認Keywords是否有18禁，有則移除。';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $keywords = KeywordClickCountModel::where('date', '>=', Carbon::now()->subDays(2)->startOfDay())
            ->orderBy('total_clicks', 'desc')
            ->groupBy('keyword')
            ->select('keyword', KeywordClickCountModel::raw('sum(click_count) as total_clicks'))
            ->limit(25)
            ->get();
        $keywords_arr = [];
        foreach ($keywords->toArray() as $key => $value) {
            $keywords_arr[$key] = $value['keyword'];
        }
        $keywords_arr = array_unique($keywords_arr);
        if ($keywords_arr) {
            $keywords_str = implode(',', $keywords_arr);
            $apiKey = env('AI_KEY', '');
            $url = env('AI_URL', '');
            try {
                $response = Http::timeout(60)->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])->post($url, [
                    'model' => env('AI_MODEL', ''),
                    'messages' => [
                        [
                            'role' => 'user',
                            "content" => "請幫我從下列資料中找出符合下列規則之關鍵字，規則的部分均使用『嚴謹度』1~10分作為依據，1分為最鬆散，10分為最嚴謹，規則如下：
                            1. 請排除不雅字詞，嚴謹度：5
                            2. 請排除18禁文字，嚴謹度：10
                            3. 請排除髒話，嚴謹度：10
                            4. 請排除性歧視字眼，嚴謹度：10
                            5. 不可任意更動原始資料順序，嚴謹度：10
                            以上條件均完全符合之結果並以json格式產出，Json格式請參考此處：'{\"keywords\":['keyword_1','keyword_2','keyword_3'...]}'，原始資料如下：" . $keywords_str . "。",
                        ],
                    ],
                    'temperature' => 1,
                ]);
                $responseData = $response->json();
                $message = '';
                if (!empty($responseData['choices'])) {
                    foreach ($responseData['choices'] as $choice) {
                        $message = $choice['message']['content'];
                    }
                    $upload_result = $this->uploadFileToS3($message);
                } else {
                    $upload_result = false;
                }

                if ($upload_result) {
                    return Command::SUCCESS;
                } else {
                    $this->error("檔案上傳失敗");
                    return 1;
                }
            } catch (ConnectionException $exception) {
                $this->error("An exception occurred: " . $exception->getMessage());
                return 1;
            }
        } else {
            $this->error("無資料");
            return 1;
        }
    }

    public function uploadFileToS3($message = '')
    {
        if ($message) {
            // 將字串上傳至 S3
            $fileName = env('AWS_S3_DEEP_FOLDER', 'health2.0-pre') . '/keyword/keywords_list.txt';
            $uploaded = Storage::disk('s3')->put($fileName, mb_convert_encoding($message, 'UTF-8'), 'public');
            if ($uploaded) {
                $this->clearCloudFrontCache();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function clearCloudFrontCache()
    {
        $distributionId = "E33B31WFEFAOKE";
        $paths = [
            '/' . env('AWS_S3_DEEP_FOLDER', 'health2.0-pre') . '/keyword/keywords_list.txt',
        ];
        // 使用您的 AWS 設定初始化 CloudFront 客戶端
        $cloudFront = new CloudFrontClient([
            'version' => 'latest',
            'region' => env("AWS_DEFAULT_REGION"),
            'credentials' => [
                'key' => env("AWS_ACCESS_KEY_ID"),
                'secret' => env("AWS_SECRET_ACCESS_KEY"),
            ],
        ]);

        // 建立無效化批次
        $invalidationBatch = [
            'Paths' => [
                'Quantity' => count($paths),
                'Items' => $paths,
            ],
            'CallerReference' => time() . '-' . uniqid(),
        ];

        // 發送無效化請求
        try {
            $result = $cloudFront->createInvalidation([
                'DistributionId' => $distributionId,
                'InvalidationBatch' => $invalidationBatch,
            ]);
        } catch (\Throwable $exception) {
            \Log::warning($exception);
        }

        return $result ?? false;
    }
}
