<?php

namespace App\Enums;

/**
 * Class ImportType
 * @package App\Enums
 */
class ImportType
{
    /** @var string 純字串 */
    public const STRING = 'string';

    /** @var string 圖片 */
    public const IMAGE = 'image';

    /** @var string 網址 */
    public const URL = 'url';

    /** @var string 枚舉 */
    public const ENUM = 'enum';

    /** @var string 棄用 */
    public const DEPRECATE = 'deprecate';

    /** @var string 陣列 */
    public const ARRAY = 'array';
}
