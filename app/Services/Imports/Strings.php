<?php


namespace App\Services\Imports;

/**
 * Class Strings
 * @package App\Services\Imports
 */
class Strings extends BaseImport implements ImportInterface
{
    /**
     * @param array $errors
     * @param $value
     * @param array $column
     * @param int $realRow
     */
    public function check(array &$errors, $value, array $column, int $realRow): void
    {
        $strLen = mb_strlen($value);

        if (($column['rules']['max'] !== null && $strLen > $column['rules']['max']) || $strLen < $column['rules']['min']) {
            $errors[] = "列：{$realRow}, {$column['column']} 欄位大小超出限制。size: {$strLen}";
        }

        if (!empty($column['not_in'])) {
            $collection = $column['not_in'][0]::where($column['not_in'][1], $value)->get();

            if ($collection->isNotEmpty()) {
                $errors[] = "列：{$realRow}, {$column['column']} 欄位不可重複。value: {$value}";
            }
        }
    }

    /**
     * @param array $queries
     * @param $value
     * @param int $rowIndex
     * @param array $column
     * @param bool $isChild
     */
    public function action(array &$queries, $value, int $rowIndex, array $column, bool $isChild = false): void
    {
        $value = trim($value);

        if (!empty($column['split'])) {
            $splitValue = explode($column['split'], $value);

            if ($isChild) {
                $queries[$rowIndex][$column['database'][0]][] = [
                    $column['database'][1] => trim($splitValue[0]),
                    $column['split_database'][1] => strpos($value, $column['split']) !== false,
                ];
            } else {
                $queries[$rowIndex][$column['database'][0]] = [
                    $column['database'][1] => trim($splitValue[0]),
                    $column['split_database'][1] => strpos($value, $column['split']) !== false,
                ];
            }
        } else {
            parent::action($queries, $value, $rowIndex, $column, $isChild);
        }
    }
}
