<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use YlsIdeas\FeatureFlags\Facades\Features;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 同步舊資料庫應由舊資料庫發起
        // $schedule->command('app:notifications')->everyMinute();

        // 同步old db clickcount
        $schedule->command('sync:clickcount')->everyThirtyMinutes()->onOneServer()->when(
            function () {
                return Features::accessible('tmp_sync_clickcount');
            }
        );

        // 同步old db keyword
        $schedule->command('sync:keyword')->everyThirtyMinutes()->onOneServer()->when(
            function () {
                return Features::accessible('tmp_sync_keyword');
            }
        );

        // 點擊數同步前一天資料
        $schedule->command('keyword_click_count:append')->dailyAt('00:00')->onOneServer()->when(
            function () {
                return Features::accessible('tmp_keyword_click_count_append');
            }
        );

        // 同步推播管理狀態
        $schedule->command('notification_status:sync')->everyThirtyMinutes()->onOneServer()->when(
            function () {
                return Features::accessible('tmp_notification_status_sync');
            }
        );

        // 同步舊後台醫學百科 -->  新後台醫學百科
        $schedule->command('db:seed --class=HealthOrgansSeeder')->everyFiveMinutes()->onOneServer()->when(
            function () {
                return Features::accessible('sync_encyclophedia_old_to_new');
            }
        );
        $schedule->command('db:seed --class=HealthSicknessSeeder')->everyFiveMinutes()->onOneServer()->when(
            function () {
                return Features::accessible('sync_encyclophedia_old_to_new');
            }
        );
        $schedule->command('db:seed --class=HealthSicknessToOrganSeeder')->everyFiveMinutes()->onOneServer()->when(
            function () {
                return Features::accessible('sync_encyclophedia_old_to_new');
            }
        );
        $schedule->command('db:seed --class=HealthArticlesCategoriesSeeder')->everyFiveMinutes()->onOneServer()->when(
            function () {
                return Features::accessible('sync_encyclophedia_old_to_new');
            }
        );

        // 產生Sitemap
        $schedule->command('sitemap:produce')->hourlyAt(36)->onOneServer()->when(
            function () {
                return Features::accessible('sitemap_produce');
            }
        );

        // Ping Sitemap
        $schedule->command('sitemap:ping')->dailyAt('00:00')->onOneServer()->when(
            function () {
                return Features::accessible('sitemap_ping');
            }
        );

        // Backup Daily Views Count
        $schedule->command('backupDailyViewsCount')->dailyAt('4:30')->onOneServer()->when(
            function () {
                return Features::accessible('backup_daily_views_count');
            }
        );

        // 產生排除性暗示之熱門關鍵字
        $schedule->command('Ai:CheckRestrictedKeywords')->everyThirtyMinutes()->onOneServer()->when(
            function () {
                return Features::accessible('AiCheckRestrictedKeywords');
            }
        );

        // 加入定期產出文章給DeepQ
        $schedule->command('HealthArticle:ExportForDeepQ')->weeklyOn(1, '3:20')->onOneServer()->when(
            function () {
                return Features::accessible('ExportForDeepQ');
            }
        );

        // 排程通知DeepQ新增修改刪除文章
        $schedule->command('deepq:daily')->dailyAt('1:00')->onOneServer()->when(
            function () {
                return Features::accessible('feature_daily_deep_article_update');
            }
        );

        // Google indexing
        $schedule->command('sync:google_indexing')->everyThirtyMinutes()->onOneServer()->when(
            function () {
                return Features::accessible('feature_google_indexing_sync');
            }
        );

        // Google indexing
        $schedule->command('generate:google_token')->hourly()->onOneServer()->when(
            function () {
                return config('indexing.default') === 'google';
            }
        );

        // aiwize rss feed
        $schedule->command('fetch:aiwize')->everyTenMinutes()->onOneServer()->when(
            function () {
                return Features::accessible('feature_aiwize_articles');
            }
        );
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        include base_path('routes/console.php');
    }
}
