<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Storage;

use SimpleXMLElement;

class SitemapPing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:ping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Submit Sitemap To Google';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = 'health2.0';
        if(!env('APP_WEB_URL') || stripos(env('APP_WEB_URL'), '-pre') || stripos(env('APP_WEB_URL'), 'local')){
            $path .= '-pre';
        }
        $path .= '/health_sitemap/sitemap.xml';

        $fileContents = Storage::disk('s3')->get($path);

        $xml = new SimpleXMLElement($fileContents);

        $sendData['text'] = "健康\n\n";
        foreach($xml->children() as $child){
            $loc = $child->loc;
            $ping_url = 'https://www.google.com/ping?sitemap='.$loc;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ping_url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $sendData['text'] .= '位置：'.$loc."\n";
            if((int)$statusCode == 200){
                $sendData['text'] .= 'Status Code： :smiley: '.$statusCode." :smiley: \n\n";
            }else{
                $sendData['text'] .= 'Status Code： :rage: '.$statusCode." :rage: \n\n";
            }
        }

        $slack_url = 'https://hooks.slack.com/services/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';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $slack_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sendData));
        curl_exec($ch);
        curl_close($ch);
        return Command::SUCCESS;
    }
}
