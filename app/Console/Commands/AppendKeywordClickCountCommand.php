<?php

namespace App\Console\Commands;

use App\Enums\DailyViewCountPlatform;
use App\Models\KeywordClickCountModel;
use Illuminate\Console\Command;

class AppendKeywordClickCountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keyword_click_count:append';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '補上前一天的點擊數';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $keywords = KeywordClickCountModel::on('mysql_tvbs_v4')
            ->where('prg_id', DailyViewCountPlatform::HEALTH)
            ->where('date', '>=', now()->subDay()->startOfDay())
            ->get();

        KeywordClickCountModel::insert($keywords->map->only(['date', 'keyword', 'click_count'])->toArray());
    }
}
