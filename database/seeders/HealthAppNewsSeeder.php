<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\App\AppNewsModel;
use Illuminate\Support\Carbon;

class HealthAppNewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $news = AppNewsModel::create([
            'message' => 'App news',
            'start' => Carbon::now()->subDays(3),
            'end' => Carbon::now()->subDays(2),
            'created_user' => 'dev001',
            'updated_user' => 'dev001'
        ]);
    }
}
