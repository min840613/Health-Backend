<?php


namespace App\Services\Imports;

/**
 * Class Url
 * @package App\Services\Imports
 */
class Url extends BaseImport implements ImportInterface
{
    /**
     * @param array $errors
     * @param $value
     * @param array $column
     * @param int $realRow
     */
    public function check(array &$errors, $value, array $column, int $realRow): void
    {
        if ($column['replace_prefix']) {
            $replaceValue = str_replace($column['prefix'], '', $value);
            [$prefix, $category, $articleId] = explode('/', $replaceValue);
            $article = $column['database'][0]::find($articleId);

            if ($article === null) {
//                $errors[] = "列：{$realRow}, {$column['column']} 找不到該文章。 {$value}";
            }
        }

        if (!empty($column['prefix']) && strpos($value, $column['prefix']) === false) {
            $errors[] = "列：{$realRow}, {$column['column']} URL 不正確。";
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
        if ($column['replace_prefix']) {
            $value = str_replace($column['prefix'], '', $value);
        }

        parent::action($queries, $value, $rowIndex, $column, $isChild);
    }
}
