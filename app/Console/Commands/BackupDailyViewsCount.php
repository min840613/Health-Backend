<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Models\DailyViewCountModel;

use App\Models\DailyViewCountOldModel;

use Illuminate\Support\Facades\DB;

class BackupDailyViewsCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backupDailyViewsCount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup three days ago data from daily_view_count to daily_view_count_old';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::transaction(function () {

            $dateCondition  =  Carbon::now()->subDay(3)->format('Y-m-d');
            $result = DailyViewCountModel::where('date', '<=', $dateCondition)
                                    ->get()
                                    ->toArray();
            foreach($result as $v){
                if($v['date'] != '0000-00-00'){
                    DailyViewCountOldModel::insert($v);
                }
                DailyViewCountModel::where('id', $v['id'])->delete();
            }
        });

        return Command::SUCCESS;
    }
}
