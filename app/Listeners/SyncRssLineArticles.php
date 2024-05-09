<?php

namespace App\Listeners;

use App\Events\ArticleSave;
use App\Models\Articles\ArticleModel;

/**
 * Class SyncRssLineArticles
 * @package App\Listeners
 */
class SyncRssLineArticles
{
    /** @var string */
    public const PLATFORM = 'health';

    /**
     * Handle the event.
     *  同步關閉這個也不能拔掉
     * @param ArticleSave $event
     * @return void
     * @throws \Throwable
     */
    public function handle(ArticleSave $event): void
    {
        if (!config('database.connections.mysql_tvbs_rss.is_sync')) {
            return;
        }

        $article = $event->getArticle();

        $rss = \DB::connection('mysql_tvbs_rss')->table('external_rss_line_program')
            ->where('news_id', $article->articles_id)
            ->where('program_id', self::PLATFORM)
            ->first();

        switch ($article->is_line_rss) {
            case 1:
                $this->rssOpen($rss, $article);
                break;
            case 0:
            default:
                $this->rssClose($rss);
                break;
        }
    }

    /**
     * @param $rss
     * @param ArticleModel $article
     */
    private function rssOpen($rss, ArticleModel $article): void
    {
        $columns = [
            'record_editor' => $article->created_user,
            'video_file_name' => $article->video_file_name,
            'program_id' => self::PLATFORM,
            'video_file_editor' => $article->created_user,
        ];

        if ($rss !== null) {
            // 如果沒供過稿（無配對日期）才進行動作
            if (is_null($rss->rss_release_date)) {
                \DB::connection('mysql_tvbs_rss')->table('external_rss_line_program')
                    ->where('sno', $rss->sno)
                    ->update($columns);
            }
        } else {
            $columns = array_merge($columns, [
                'news_id' => $article->articles_id,
                'status' => 1,
                'record_create_time' => now()->toDateTimeString(),
            ]);
            \DB::connection('mysql_tvbs_rss')->table('external_rss_line_program')->insert($columns);
        }
    }

    /**
     * @param $rss
     */
    private function rssClose($rss): void
    {
        if ($rss !== null && is_null($rss->rss_release_date)) {
            \DB::connection('mysql_tvbs_rss')->table('external_rss_line_program')->where('sno', $rss->sno)->delete();
        }
    }
}
