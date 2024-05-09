<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

/**
 * Class UrlHelper
 * @package App\Helpers
 */
class UrlHelper
{
    /**
     * @param string $value
     * @return bool
     */
    public static function isUrl(string $value): bool
    {
        return preg_match('#^https?://#', $value);
    }

    /**
     * @param string $url
     * @return string
     */
    public static function parseUrl(string $url): string
    {
        if (empty($url)) {
            return ImageHelper::DEFAULT_URL;
        }

        $replace = [
            '/news_detail/' => \Storage::disk('s3_old')->url('news4.0/img/news_detail/'),
            '/mirrormedia/piwigo/_data/' => \Storage::disk('s3_old')->url('img/mirrormedia/_data/'),
            '/mirrormedia/piwigo/upload/' => \Storage::disk('s3_old')->url('img/mirrormedia/upload/'),
            '/piwigo/_data/' => \Storage::disk('s3_old')->url('img/_data/'),
            '/piwigo/upload/' => \Storage::disk('s3_old')->url('img/upload/'),
            '/tvbs_piwigo/upload/' => \Storage::disk('s3_old')->url('img/upload/'),
            '/piwigo/talk/' => \Storage::disk('s3_old')->url('img/talk/'),
            '/news/images/' => \Storage::disk('s3_old')->url('news/images/'),
            '/piwigo/show/' => \Storage::disk('s3_old')->url('img/show/'),
            '/piwigo/supertaste/' => \Storage::disk('s3_old')->url('img/supertaste/'),
            '/piwigo/woman/' => \Storage::disk('s3_old')->url('img/woman/'),
            '/piwigo/work/' => \Storage::disk('s3_old')->url('img/work/'),
            '/piwigo/drama/' => \Storage::disk('s3_old')->url('img/drama/'),
            '/program_piwigo/piwigo/' => \Storage::disk('s3_old')->url('img/program/'),
            'video/app_push/' => \Storage::disk('s3_old')->url('video/app_push/'),
            't_reporter/' => \Storage::disk('s3_old')->url('news4.0/upload/t_reporter/'),
        ];

        return strtr($url, $replace);
    }

    /**
     * @param string $url
     * @return string
     */
    public static function s3Url(string $url): string
    {
        return Storage::disk('s3_old')->url($url);
    }

    /**
     * @param string $url
     * @return string
     */
    public static function s3ImagesPath(string $url): string
    {
        return Storage::disk('s3_old')->url('program/woman' . ((env('APP_ENV') == 'production') ? '' : '-pre') . '/images' . $url);
    }

    /**
     * @param int $articleId
     * @param string $categoryName
     * @return string
     */
    public static function generateWebUrl(int $articleId, string $categoryName): string
    {
        return config('app.web_url') . '/' . $categoryName . '/' . $articleId;
    }
}
