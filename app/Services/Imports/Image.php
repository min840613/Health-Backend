<?php


namespace App\Services\Imports;

/**
 * Class Image
 * @package App\Services\Imports
 */
class Image extends BaseImport implements ImportInterface
{
    /**
     * @param string $filename
     * @param array $allowFileExtensions
     * @return bool
     */
    private function isAllowExtension(string $filename, array $allowFileExtensions): bool
    {
        return in_array(pathinfo($filename, PATHINFO_EXTENSION), $allowFileExtensions, true);
    }

    /**
     * @param array $errors
     * @param $value
     * @param array $column
     * @param int $realRow
     */
    public function check(array &$errors, $value, array $column, int $realRow): void
    {
        if (!empty($column['prefix']) && strpos($value, $column['prefix']) === false) {
            $errors[] = "列：{$realRow}, {$column['column']} 圖片 不正確。";
        }

        if (!$this->isAllowExtension($value, $column['allow_file_extension'])) {
            $errors[] = "列：{$realRow}, {$column['column']} 為不允許的圖片副檔名。({$value})";
        }
    }
}
