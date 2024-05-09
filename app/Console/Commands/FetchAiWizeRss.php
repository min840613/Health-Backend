<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aiwize\AiwizeModel;
use Illuminate\Support\Carbon;

class FetchAiWizeRss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:aiwize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Rss From Ai Wize';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $xmlString = file_get_contents(config('aiwize.rss_url'));
        $xml = simplexml_load_string($xmlString, null, LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        if (isset($array['item']) && $array['item']) {
            foreach ($array['item'] as $value) {
                $article = AiwizeModel::where('ai_wize_id', $value['id'])->first();
                if (!$article) {
                    $item['ai_wize_id'] = $value['id'];
                    $item['ai_wize_publish'] = Carbon::parse($value['pubDate'])->setTimezone('Asia/Taipei')->format('Y-m-d H:i:s');
                    $item['long_title'] = ($value['longTitles']) ? trim($value['longTitles'], '"') : 'no long title detected';
                    $item['short_title'] = ($value['shortTitles']) ? trim($value['shortTitles'], '"') : 'no short title detected';
                    $item['content'] = ($value['content']) ? $value['content'] : 'no content detected';
                    $item['keyword'] = ($value['keywords']) ? $value['keywords'] : 'no keyword detected';

                    AiwizeModel::create($item);
                }
            }
        }

        return Command::SUCCESS;
    }
}
