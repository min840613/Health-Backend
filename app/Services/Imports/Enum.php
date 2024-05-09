<?php


namespace App\Services\Imports;

/**
 * Class Enum
 * @package App\Services\Imports
 */
class Enum extends BaseImport implements ImportInterface
{
    /** @var array */
    private array $enums;

    /**
     * @param array $enums
     * @return Enum
     */
    public function setEnums(array $enums): Enum
    {
        $this->enums = $enums;

        return $this;
    }

    /**
     * @param array $errors
     * @param $value
     * @param array $column
     * @param int $realRow
     */
    public function check(array &$errors, $value, array $column, int $realRow): void
    {
        if (!empty($column['mappings']) && !array_key_exists($value, $column['mappings'])) {
            $errors[] = "列：{$realRow}, {$column['column']} 錯誤，應為 enum 其一。";
        }

        if (!empty($column['mapping_in']) && $this->enums[$column['mapping_in'][0]]->where($column['mapping_in'][1], $value)->isEmpty()) {
            $errors[] = "列：{$realRow}, {$column['column']} 錯誤，應為 enum 其一。 ({$value})";
        }

        if (
            !empty($column['mapping_in']) &&
            !empty($column['mapping_in'][2]) &&
            $column['mapping_in'][2] === true &&
            $this->enums[$column['mapping_in'][0]]->where($column['mapping_in'][1], $value)->where('status', 1)->isEmpty()
        ) {
            $errors[] = "列：{$realRow}, {$column['column']} 錯誤，該欄位的狀態未啟用。 ({$value})";
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
        if (isset($column['mappings'][$value]) && in_array(trim($value), ['TRUE', 'FALSE'])) {
            $value = $column['mappings'][$value];
        }

        parent::action($queries, $value, $rowIndex, $column, $isChild);
    }
}
