<?php

namespace App\Enums;

/**
 * Class NotificationsType
 * @package App\Enums
 */
class NotificationsType
{
    /** @var int 文章 */
    public const ARTICLE = 1;

    /** @var int 訊息通知 */
    public const SYSTEM = 3;

    /** @var int 活動公告 */
    public const ACTIVITY = 4;

    /** @var int 搖一搖 */
    public const SHAKE = 5;
}
