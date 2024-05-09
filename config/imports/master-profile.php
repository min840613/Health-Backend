<?php

use App\Enums\ImportType;
use App\Models\Articles\ArticleModel;
use App\Models\Masters\DivisionsModel;
use App\Models\Masters\InstitutionsModel;
use App\Models\Masters\MasterDivisionModel;
use App\Models\Masters\MasterExperiencesModel;
use App\Models\Masters\MasterExpertiseModel;
use App\Models\Masters\MastersModel;

return [
    [
        'column' => '醫師名字',
        'type' => ImportType::STRING,
        'split' => null,
        'rules' => [
            'min' => 1,
            'max' => 50,
        ],
        'mapping_in' => [],
        'database' => [MastersModel::class, 'name'],
    ],
    [
        'column' => '醫師英文名稱',
        'type' => ImportType::STRING,
        'split' => null,
        'rules' => [
            'min' => 1,
            'max' => 50,
        ],
        'mapping_in' => [],
        'not_in' => [MastersModel::class, 'en_name'],
        'database' => [MastersModel::class, 'en_name'],
    ],
    [
        'column' => '照片(棄置)',
        'type' => ImportType::DEPRECATE,
        'split' => null,
        'rules' => [
            'min' => 1,
            'max' => 50,
        ],
        'mapping_in' => [],
    ],
    [
        'column' => '目前所屬醫院',
        'type' => ImportType::ENUM,
        'split' => null,
        'rules' => [
            'min' => 1,
            'max' => 10,
        ],
        'mapping_in' => [InstitutionsModel::class, 'name'],
        'database' => [MastersModel::class, 'institution_id'],
    ],
    [
        'column' => '醫院',
        'type' => ImportType::ENUM,
        'split' => null,
        'rules' => [
            'min' => 1,
            'max' => 50,
        ],
        'mapping_in' => [InstitutionsModel::class, 'nick_name'],
        'database' => [MastersModel::class, 'institution_id'],
    ],
    [
        'column' => '目前職稱',
        'type' => ImportType::STRING,
        'split' => null,
        'rules' => [
            'min' => 1,
            'max' => 255,
        ],
        'mapping_in' => [],
        'database' => [MastersModel::class, 'title'],
    ],
    [
        'column' => '主科別',
        'type' => ImportType::ARRAY,
        'split' => PHP_EOL,
        'rules' => [
            'min' => 1,
            'max' => 10,
        ],
        'mapping_in' => [],
        'children' => [
            'column' => '主科別(children)',
            'type' => ImportType::ENUM,
            'split' => null,
            'rules' => [
                'min' => 1,
                'max' => 255,
            ],
            // 第三個參數表示狀態是否啟用的驗證
            'mapping_in' => [DivisionsModel::class, 'name', true],
            'database' => [MasterDivisionModel::class, 'division_id'],
        ],
    ],
    [
        'column' => '科別',
        'type' => ImportType::ARRAY,
        'split' => PHP_EOL,
        'rules' => [
            'min' => 0,
            'max' => 255,
        ],
        'mapping_in' => [],
        'children' => [
            'column' => '科別(children)',
            'type' => ImportType::STRING,
            'split' => null,
            'rules' => [
                'min' => 1,
                'max' => 255,
            ],
            'mapping_in' => [],
            'database' => [MasterDivisionModel::class, 'description'],
        ],
    ],
    [
        'column' => '經歷',
        'type' => ImportType::ARRAY,
        'split' => PHP_EOL,
        'rules' => [
            'min' => 1,
            'max' => 8,
        ],
        'mapping_in' => [],
        'children' => [
            'column' => '經歷(children)',
            'type' => ImportType::STRING,
            'split' => '（現職）',
            'rules' => [
                'min' => 1,
                'max' => 255,
            ],
            'mapping_in' => [],
            'database' => [MasterExperiencesModel::class, 'name'],
            'split_database' => [MasterExperiencesModel::class, 'is_current_job'],
        ],
    ],
    [
        'column' => '專長',
        'type' => ImportType::ARRAY,
        'split' => '、',
        'rules' => [
            'min' => 1,
            'max' => 50,
        ],
        'mapping_in' => [],
        'children' => [
            'column' => '專長(children)',
            'type' => ImportType::STRING,
            'split' => null,
            'rules' => [
                'min' => 1,
                'max' => 255,
            ],
            'mapping_in' => [],
            'database' => [MasterExpertiseModel::class, 'name'],
        ],
    ],
    [
        'column' => '官網相關文章網址',
        'type' => ImportType::ARRAY,
        'split' => PHP_EOL,
        'rules' => [
            'min' => 1,
            'max' => 100,
        ],
        'mapping_in' => [],
        'children' => [
            'column' => '官網相關文章網址(children)',
            'type' => ImportType::URL,
            'split' => null,
            'rules' => [
                'min' => 1,
                'max' => null,
            ],
            'mapping_in' => [],
            'prefix' => env('APP_WEB_URL'),
            'replace_prefix' => true,
            // 該文章增加該醫師
            'database' => [ArticleModel::class, 'talent_category_id'],
        ],
    ],
    [
        'column' => '是否為醫學中心(棄置)',
        'type' => ImportType::DEPRECATE,
        'split' => null,
        'rules' => [
            'min' => 1,
            'max' => null,
        ],
        'mapping_in' => [],
    ],
    [
        'column' => '醫級專家(棄置)',
        'type' => ImportType::DEPRECATE,
        'split' => null,
        'rules' => [
            'min' => 1,
            'max' => null,
        ],
        'mapping_in' => [],
    ],
    [
        'column' => '醫級列表圖片url',
        'type' => ImportType::IMAGE,
        'split' => null,
        'rules' => [
            'min' => 1,
            'max' => null,
        ],
        'mapping_in' => [],
        'prefix' => env('AWS_S3_URL'),
        'allow_file_extension' => ['jpg', 'jpeg', 'png'],
        'database' => [MastersModel::class, 'image'],
    ],
    [
        'column' => '醫師Profile url',
        'type' => ImportType::IMAGE,
        'split' => null,
        'rules' => [
            'min' => 1,
            'max' => null,
        ],
        'mapping_in' => [],
        'prefix' => env('AWS_S3_URL'),
        'allow_file_extension' => ['jpg', 'jpeg', 'png'],
        'database' => [MastersModel::class, 'content_image'],
    ],
    [
        'column' => '簽約醫師',
        'type' => ImportType::ENUM,
        'split' => null,
        'rules' => [
            'min' => 1,
            'max' => null,
        ],
        'mapping_in' => [],
        'mappings' => [
            'TRUE' => 1,
            'FALSE' => 0,
        ],
        'database' => [MastersModel::class, 'is_contracted'],
    ],
    [
        'column' => '狀態',
        'type' => ImportType::ENUM,
        'split' => null,
        'rules' => [
            'min' => 1,
            'max' => null,
        ],
        'mapping_in' => [],
        'mappings' => [
            'TRUE' => 1,
            'FALSE' => 0,
        ],
        'database' => [MastersModel::class, 'status'],
    ],
];
