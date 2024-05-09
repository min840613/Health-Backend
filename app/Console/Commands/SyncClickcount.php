<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use YlsIdeas\FeatureFlags\Facades\Features;
use App\Models\DailyViewCountModel;
use App\Enums\DailyViewCountPlatform;

class SyncClickcount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:clickcount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync clickcount from tvbs_v4';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $flag = Features::accessible('tmp_sync_clickcount');

        if ($flag) {
            $clicks = \DB::connection('mysql_tvbs_old')
                ->table('daily_views_count')
                ->where([
                    ['prg_id', '=', DailyViewCountPlatform::HEALTH],
                    ['date', '=', Carbon::now()->toDateString()]
                ])->get();

            if ($clicks) {
                foreach ($clicks as $click) {
                    $dailyViewCount = DailyViewCountModel::updateOrCreate(
                        [
                            'date' => $click->date,
                            'source_type' => $click->source_type,
                            'source_id' => $click->source_id
                        ],
                        ['click_count' => $click->click_count]
                    );
                }
            }
            $this->info('daily view count sync success');
        } else {
            $this->info('This feature is off');
        }

    }
}
