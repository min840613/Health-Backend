<?php

namespace App\Helpers;

/**
 * Class ImageHelper
 * @package App\Helpers
 */
class ImageHelper
{
    /** @var string  */
    public const DEFAULT_URL = 'https://cc.tvbs.com.tw/img/program/upload/2018/06/13/20180613162350-6076d86f.jpg';

    /**
     * 產縮圖
     *  - 舊的產縮圖方式整理
     * @param string $url
     * @param string $type
     * @return string
     */
    public static function thumbnail(string $url, string $type = '-xs'): string
    {
        $urlAnalyze = explode('.', $url);

        if (!in_array($type, ['-me', '-sm', '-sq', '-th', '-xs'])) {
            return $url;
        }

        switch (true) {
            case strpos($url, 'https://cc.tvbs.com.tw'):
                //網址已是cdn
                return self::cdnUrl($url, $type);
                break;
            case strpos($url, 'program_piwigo/piwigo/_data/i/upload/2'):
                //已是縮圖的圖
                return self::thumbnailTrans($url, $type, $urlAnalyze);
                break;
            case strpos($url, 'program_piwigo/piwigo/upload/2'):
                //program圖庫
                return self::fromProgramGallery($url, $type, $urlAnalyze);
                break;
            case strpos($url, 'piwigo/_data/i/upload/2'):
                //新聞圖庫已是縮圖
                return self::newsGalleryThumbnailTrans($url, $type, $urlAnalyze);
                break;
            case strpos($url, 'piwigo/upload/2'):
                //新聞圖庫
                return self::newsGallery($url, $type, $urlAnalyze);
                break;
            default:
                return $url;
                break;
        }
    }

    /**
     * @param string $url
     * @return mixed
     */
    private static function checkGenerated(string $url)
    {
        $ch2 = curl_init($url);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
        curl_exec($ch2);
        $httpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        curl_close($ch2);

        return $httpCode;
    }

    /**
     * 網址已是 cdn
     * @param string $url
     * @param string $type
     * @return string
     */
    private static function cdnUrl(string $url, string $type): string
    {
        $pure = str_replace('https://cc.tvbs.com.tw', '', $url);
        $urlAnalyze = explode('.', $pure);
        $isThumbnail = strpos($pure, '/_data/i/upload/2'); //已是縮圖的圖
        $isMain = strpos($pure, '/upload/2'); //2000年後的圖
        if ($isThumbnail !== false) {
            if (substr($urlAnalyze[0], -3) == $type) {
                return $url;
            }

            $urlPure = substr($urlAnalyze[0], 0, -3);
            $thumbnailUrl = 'https://cc.tvbs.com.tw' . $urlPure . $type . '.' . $urlAnalyze[1];

            $httpCode = self::checkGenerated($thumbnailUrl);

            return ($httpCode !== 200) ? $url : $thumbnailUrl;
        } else if ($isMain !== false) {
            $pure_url = $urlAnalyze[0] . $type . '.' . $urlAnalyze[1];
            $thumbnailUrl = 'https://cc.tvbs.com.tw' . str_replace('/upload/', '/_data/i/upload/', $pure_url);

            $httpCode = self::checkGenerated($thumbnailUrl);

            return ($httpCode !== 200) ? $url : $thumbnailUrl;
        }

        return $url;
    }

    /**
     * 已是縮圖的圖
     * @param string $url
     * @param string $type
     * @param array $urlAnalyze
     * @return string
     */
    private static function thumbnailTrans(string $url, string $type, array $urlAnalyze): string
    {
        if (substr($urlAnalyze[0], -3) == $type) {
            return $url;
        }

        $urlPure = substr($urlAnalyze[0], 0, -3);
        $thumbnailUrl = $urlPure . $type . '.' . $urlAnalyze[1];

        $try_url = str_replace('program_piwigo/piwigo', 'img/program', $thumbnailUrl);
        $httpCode = self::checkGenerated('https://cc.tvbs.com.tw' . $try_url);

        return ($httpCode !== 200) ? $url : $thumbnailUrl;
    }

    /**
     * program圖庫
     * @param string $url
     * @param string $type
     * @param array $urlAnalyze
     * @return string
     */
    private static function fromProgramGallery(string $url, string $type, array $urlAnalyze): string
    {
        $url = $urlAnalyze[0] . $type . '.' . $urlAnalyze[1];
        $thumbnailUrl = str_replace('program_piwigo/piwigo/upload/', 'program_piwigo/piwigo/_data/i/upload/', $url);

        $try_url = str_replace('program_piwigo/piwigo', 'img/program', $thumbnailUrl);
        $httpCode = self::checkGenerated('https://cc.tvbs.com.tw' . $try_url);

        if ($httpCode !== 200) {
            $urlAnalyze2 = explode('.', $url);
            $new_url = substr($urlAnalyze2[0], 0, -3);
            $url = $new_url . '.' . $urlAnalyze2[1];
            return $url;
        }
        return $thumbnailUrl;
    }

    /**
     * 新聞圖庫已是縮圖
     * @param string $url
     * @param string $type
     * @param array $urlAnalyze
     * @return string
     */
    private static function newsGalleryThumbnailTrans(string $url, string $type, array $urlAnalyze): string
    {
        if (substr($urlAnalyze[0], -3) == $type) {
            return $url;
        }

        $urlPure = substr($urlAnalyze[0], 0, -3);
        $thumbnailUrl = $urlPure . $type . '.' . $urlAnalyze[1];

        $try_url = str_replace('piwigo', 'img', $thumbnailUrl);
        $httpCode = self::checkGenerated('https://cc.tvbs.com.tw' . $try_url);

        return ($httpCode !== 200) ? $url : $thumbnailUrl;
    }

    /**
     * 新聞圖庫
     * @param string $url
     * @param string $type
     * @param array $urlAnalyze
     * @return string
     */
    private static function newsGallery(string $url, string $type, array $urlAnalyze): string
    {
        $url = $urlAnalyze[0] . $type . '.' . $urlAnalyze[1];
        $thumbnailUrl = str_replace('piwigo/upload/', 'piwigo/_data/i/upload/', $url);

        $try_url = str_replace('piwigo', 'img', $thumbnailUrl);
        $httpCode = self::checkGenerated('https://cc.tvbs.com.tw' . $try_url);

        if ($httpCode !== 200) {
            $urlAnalyze2 = explode('.', $url);
            $new_url = substr($urlAnalyze2[0], 0, -3);
            $url = $new_url . '.' . $urlAnalyze2[1];
            return $url;
        }
        return $thumbnailUrl;
    }

    public static function judgeImageSize(string $url, int $width, int $height): string
    {
        $errMsg = '';
        $image_info = getimagesize($url);

        if ($image_info[0] != $width || $image_info[1] != $height) {
            $errMsg = '目前圖片大小為' . $image_info[0] . 'x' . $image_info[1] . "\n" . '限制尺寸：' . $width . 'x' . $height . "\n";
        }

        return $errMsg;
    }
}
