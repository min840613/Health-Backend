<?php

namespace App\Services\Imports;

/**
 * Class BaseImport
 * @package App\Services\Imports
 */
class BaseImport
{
    /**
     * @param array $queries
     * @param $value
     * @param int $rowIndex
     * @param array $column
     * @param bool $isChild
     */
    public function action(array &$queries, $value, int $rowIndex, array $column, bool $isChild = false): void
    {
        if ($isChild) {
            $queries[$rowIndex][$column['database'][0]][$column['database'][1]][] = $value;
        } else {
            $queries[$rowIndex][$column['database'][0]][$column['database'][1]] = $value;
        }
    }
}
