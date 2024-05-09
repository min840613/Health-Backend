<?php

namespace App\Enums;

/**
 * Class NotificationsStatus
 * @package App\Enums
 */
class NotificationsStatus
{
    /** @var int 尚未推播 */
    public const PENDING = 1;

    /** @var int 推播成功 */
    public const SUCCESS = 2;

    /** @var int 取消推播 */
    public const CANCEL = 3;

    /** @var int 推播失敗 */
    public const FAILED = 4;

    /** @var int 推播發送中 */
    public const PUSHING = 5;
}
