<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use YlsIdeas\FeatureFlags\Facades\Features;
use App\Models\KeywordClickCountModel;
use App\Enums\DailyViewCountPlatform;

class SyncKeyword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:keyword';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync keyword count from tvbs_v4';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $flag = Features::accessible('tmp_sync_keyword');

        if ($flag) {
            $keywords = \DB::connection('mysql_tvbs_old')
                ->table('keyword_click_count')
                ->where([
                    ['prg_id', '=', DailyViewCountPlatform::HEALTH],
                    ['date', '=', Carbon::now()->toDateString()]
                ])->get();

            if ($keywords) {
                foreach ($keywords as $keyword) {
                    $keywordClickCount = KeywordClickCountModel::updateOrCreate(
                        [
                            'date' => $keyword->date,
                            'keyword' => $keyword->keyword
                        ],
                        ['click_count' => $keyword->click_count]
                    );
                }
            }

            $this->info('keyword click count sync success');
        } else {
            $this->info('This feature is off');
        }
    }
}
