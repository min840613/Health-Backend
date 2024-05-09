<?php


namespace App\Services\Imports;

/**
 * Class Deprecate
 * @package App\Services\Imports
 */
class Deprecate extends BaseImport implements ImportInterface
{
    /**
     * @param array $errors
     * @param $value
     * @param array $column
     * @param int $realRow
     */
    public function check(array &$errors, $value, array $column, int $realRow): void
    {
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
    }
}
