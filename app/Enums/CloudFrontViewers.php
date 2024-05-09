<?php

namespace App\Enums;

/**
 * Class CloudFrontViewers
 * @package App\Enums
 */
class CloudFrontViewers
{
    public const ANDROID = 'CloudFront-Is-Android-Viewer';
    public const WEB = 'CloudFront-Is-Desktop-Viewer';
    public const IOS = 'CloudFront-Is-IOS-Viewer';
}
