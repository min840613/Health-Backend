<?php

namespace App\Helpers;

use App\Enums\CloudFrontViewers;

/**
 * Class DetectionHelper
 * @package App\Helpers
 */
class DetectionHelper
{
    public static function isMobile(): bool
    {
        return self::isIos() || self::isAndroid();
    }

    public static function isIos(): bool
    {
        return request()->header(CloudFrontViewers::IOS) === 'true';
    }

    public static function isAndroid(): bool
    {
        return request()->header(CloudFrontViewers::ANDROID) === 'true';
    }
}
