<?php

namespace App\Console\Commands;

use App\Enums\ImportType;
use App\Enums\MasterType;
use App\Models\Articles\ArticleModel;
use App\Models\Masters\DivisionsModel;
use App\Models\Masters\InstitutionsModel;
use App\Models\Masters\MasterDivisionModel;
use App\Models\Masters\MasterExperiencesModel;
use App\Models\Masters\MasterExpertiseModel;
use App\Models\Masters\MastersModel;
use App\Services\Imports\Arrays;
use App\Services\Imports\Deprecate;
use App\Services\Imports\Enum;
use App\Services\Imports\Image;
use App\Services\Imports\Strings;
use App\Services\Imports\Url;
use Illuminate\Console\Command;

/**
 * Class ImportMasterProfileCommand
 * @package App\Console\Commands
 */
class ImportMasterProfileCommand extends Command
{
    /** @var int 表頭數 */
    private const IGNORE_INDEX = 2;

    /** @var string 檔名位置 */
    private const FILENAME = 'master_profile.csv';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'master_profile:import {--debug_mode=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '匯入醫級專家-醫師profile資料到管理後台';

    /** @var array */
    private array $enums;

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \JsonException
     * @throws \Throwable
     */
    public function handle(): void
    {
        ini_set("memory_limit", "2048M");

        $columns = config('imports.master-profile');

        $rows = $this->getByFile(public_path(self::FILENAME));

        $this->enums = [
            InstitutionsModel::class => InstitutionsModel::all(),
            DivisionsModel::class => DivisionsModel::all(),
        ];

        $errors = $this->check($columns, $rows);

        if (!empty($errors)) {
            if ($this->option('debug_mode') === 'true') {
                dd(json_encode($errors, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
            }
            return;
        }

        $queries = $this->buildQueries($columns, $rows);

        if ($this->option('debug_mode') === 'true') {
            dd($queries);
        }

        \DB::transaction(function () use ($queries) {
            $this->action($queries);
        });
    }

    /**
     * @param string $path
     * @return array
     */
    public function getByFile(string $path): array
    {
        $file = fopen($path, 'rb');

        $rows = [];
        while ($row = fgetcsv($file)) {
            $rows[] = $row;
        }

        fclose($file);

        return $rows;
    }

    /**
     * @param array $columns
     * @param array $data
     * @return array
     */
    public function check(array $columns, array $data): array
    {
        $errors = [];
        foreach ($data as $index => $items) {
            if ($index < self::IGNORE_INDEX) {
                continue;
            }

            foreach ($columns as $i => $column) {
                $realRow = $index + self::IGNORE_INDEX;
                if (!isset($items[$i])) {
                    $errors[] = "列：{$realRow}, {$column['column']} 不存在。";
                    continue;
                }

                $this->factory($column, $items, $realRow, $i, $errors);
            }
        }

        return $errors;
    }

    /**
     * @param array $column
     * @param array $items
     * @param int $realRow
     * @param int $norm
     * @param array $errors
     */
    private function factory(array $column, array $items, int $realRow, int $norm, array &$errors): void
    {
        $value = $items[$norm];

        switch ($column['type']) {
            case ImportType::STRING:
                app(Strings::class)->check($errors, $items[$norm], $column, $realRow);
                break;
            case ImportType::URL:
                app(Url::class)->check($errors, $items[$norm], $column, $realRow);
                break;
            case ImportType::IMAGE:
                app(Image::class)->check($errors, $items[$norm], $column, $realRow);
                break;
            case ImportType::ARRAY:
                app(Arrays::class)->check($errors, $items[$norm], $column, $realRow);

                if (!empty($value)) {
                    $children = explode($column['split'], $value);

                    foreach ($children as $childIndex => $child) {
                        $this->factory($column['children'], $children, $realRow, $childIndex, $errors);
                    }
                }
                break;
            case ImportType::ENUM:
                app(Enum::class)->setEnums($this->enums)->check($errors, $items[$norm], $column, $realRow);
                break;
            case ImportType::DEPRECATE:
                app(Deprecate::class)->check($errors, $items[$norm], $column, $realRow);
                break;
            default:
                $errors[] = "列：{$realRow}, 錯誤的 type";
                break;
        }
    }

    /**
     * @param array $columns
     * @param array $data
     * @return array
     */
    public function buildQueries(array $columns, array $data): array
    {
        $queries = [];
        foreach ($data as $index => $items) {
            if ($index < self::IGNORE_INDEX) {
                continue;
            }

            foreach ($columns as $i => $column) {
                $this->buildQueriesFactory($column, $items, $i, $index, $queries);
            }
        }

        return $queries;
    }

    /**
     * @param array $column
     * @param array $items
     * @param int $i
     * @param int $index
     * @param array $queries
     * @param bool $isChild
     */
    public function buildQueriesFactory(array $column, array $items, int $i, int $index, array &$queries, bool $isChild = false): void
    {
        switch ($column['type']) {
            case ImportType::STRING:
                app(Strings::class)->action($queries, $items[$i], $index, $column, $isChild);
                break;
            case ImportType::URL:
                app(Url::class)->action($queries, $items[$i], $index, $column, $isChild);
                break;
            case ImportType::IMAGE:
                app(Image::class)->action($queries, $items[$i], $index, $column, $isChild);
                break;
            case ImportType::ARRAY:
                if (!empty($items[$i])) {
                    $children = explode($column['split'], $items[$i]);

                    foreach ($children as $childIndex => $child) {
                        $this->buildQueriesFactory($column['children'], $children, $childIndex, $index, $queries, true);
                    }
                }
                break;
            case ImportType::ENUM:
                app(Enum::class)->setEnums($this->enums)->action($queries, $items[$i], $index, $column, $isChild);
                break;
            case ImportType::DEPRECATE:
                app(Deprecate::class)->action($queries, $items[$i], $index, $column, $isChild);
                break;
        }
    }

    /**
     * @param array $queries
     */
    public function action(array $queries): void
    {
        $users = [
            'created_user' => 'System Import',
            'updated_user' => 'System Import',
        ];

        foreach ($queries as $index => $query) {
            if (empty($query[MastersModel::class])) {
                dd('找不到主資料表的資料，無法繼續');
            }

            $query[MastersModel::class]['institution_id'] = $this->enums[InstitutionsModel::class]->where('nick_name', $query[MastersModel::class]['institution_id'])->first()->id;

            $master = app(MastersModel::class)::create(array_merge(['type' => MasterType::EXPERT], $users, $query[MastersModel::class]));

            collect($query)->except([MastersModel::class])->each(function ($item, $model) use ($master, $users) {
                switch ($model) {
                    case MasterDivisionModel::class:
                        foreach($item as $col => $value){
                            if ($col !== 'division_id') {
                                continue;
                            }

                            foreach ($value as $i => $v) {
                                $targetDivision = $this->enums[DivisionsModel::class]->where('name', $v)->first();

                                $description = isset($item['description'][$i]) && $item['description'][$i] !== 'null' ? $item['description'][$i] : null;

                                $master->divisions()->create(array_merge(['division_id' => $targetDivision->id, 'description' => $description], $users));
                            }
                        }
                        break;
                    case MasterExperiencesModel::class:
                        foreach ($item as $value) {
                            $master->experiences()->create(array_merge($value, $users));
                        }
                        break;
                    case MasterExpertiseModel::class:
                        foreach ($item as $col => $value) {
                            foreach ($value as $v) {
                                $master->expertise()->create(array_merge([$col => $v], $users));
                            }
                        }
                        break;
                    case ArticleModel::class:
                        foreach ($item as $col => $value) {
                            foreach ($value as $v) {
                                [$prefix, $category, $articleId] = explode('/', $v);
                                ArticleModel::where('articles_id', trim($articleId))->update([$col => $master->id]);
                            }
                        }
                        break;
                    default:
                        dd("未預期的 model: {$model}");
                        break;
                }
            });
        }
    }
}
