<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GoogleIndexingToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:google_token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '執行 google indexing 產生 token。';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $token = \Indexing::driver('google')->generateToken();

        \Indexing::driver('google')->keepToken(config('indexing.token_name'), $token['accessToken'], now()->addHour());
    }
}
