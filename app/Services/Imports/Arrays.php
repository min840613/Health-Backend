<?php


namespace App\Services\Imports;

/**
 * Class Arrays
 * @package App\Services\Imports
 */
class Arrays extends BaseImport implements ImportInterface
{
    /**
     * @param array $errors
     * @param $value
     * @param array $column
     * @param int $realRow
     */
    public function check(array &$errors, $value, array $column, int $realRow): void
    {
        if (!empty($value)) {
            $children = explode($column['split'], $value);

            $lens = count($children);

            if ($lens > $column['rules']['max'] || $lens < $column['rules']['min']) {
                $errors[] = "列：{$realRow}, {$column['column']} 欄位大小超出限制。size: {$lens}";
            }
        }
    }
}
