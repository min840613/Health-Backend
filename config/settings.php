<?php

return [
    'views' => [
        'sickness' => [
            'edit_field' => [
                [
                    'title' => '名稱',
                    'type' => 'text',
                    'name' => 'name',
                    'placeholder' => '請輸入名稱',
                    'required' => TRUE,
                    'id' => 'name',
                    'class' => 'col-10',
                    'warning_word' => '最多10字內'
                ],
                [
                    'title' => '狀態',
                    'type' => 'select',
                    'name' => 'status',
                    'placeholder' => '',
                    'value' => 1,
                    'required' => TRUE,
                    'id' => 'status',
                    'option' => [
                        1 => '上架',
                        0 => '下架',
                    ],
                ],
            ],
        ],
        'organs' => [
            'edit_field' => [
                [
                    'title' => '器官與組織名稱',
                    'type' => 'text',
                    'name' => 'name',
                    'placeholder' => '請輸入器官與組織名稱',
                    'required' => TRUE,
                    'id' => 'name',
                    'class' => 'col-10',
                    'warning_word' => '最多10字內'
                ],
            ],
        ],
        'divisions' => [
            'edit_field' => [
                [
                    'title' => '科別管理',
                    'type' => 'text',
                    'name' => 'name',
                    'placeholder' => '請輸入科別',
                    'required' => TRUE,
                    'id' => 'name',
                    'class' => 'col-10',
                    'warning_word' => '最多10字內'
                ],
                [
                    'title' => '英文名稱',
                    'type' => 'text',
                    'name' => 'en_name',
                    'placeholder' => '請輸入科別英文名稱',
                    'required' => TRUE,
                    'id' => 'en_name',
                    'class' => 'col-10',
                    'warning_word' => '最多15字內(僅能輸入小寫英文)'
                ],
            ],
        ],
        'institutions' => [
            'edit_field' => [
                [
                    'title' => '醫療院所',
                    'type' => 'text',
                    'name' => 'name',
                    'placeholder' => '請輸入醫療院所',
                    'required' => TRUE,
                    'id' => 'name',
                    'class' => 'col-10',
                    'warning_word' => '最多50字內'
                ],
                [
                    'title' => '英文名稱',
                    'type' => 'text',
                    'name' => 'en_name',
                    'placeholder' => '請輸入科別英文名稱',
                    'required' => TRUE,
                    'id' => 'en_name',
                    'class' => 'col-10',
                    'warning_word' => '最多15字內(僅能輸入小寫英文)'
                ],
                [
                    'title' => '簡稱',
                    'type' => 'text',
                    'name' => 'nick_name',
                    'placeholder' => '請輸入簡稱',
                    'required' => TRUE,
                    'id' => 'nick_name',
                    'class' => 'col-10',
                    'warning_word' => '最多10字內'
                ],
            ],
        ],
        'deepq_keyword' => [
            'edit_field' => [
                [
                    'title' => '關鍵字',
                    'type' => 'text',
                    'name' => 'keyword',
                    'placeholder' => '請輸入關鍵字',
                    'required' => true,
                    'id' => 'keyword',
                    'class' => 'col-10',
                    'warning_word' => null,
                    'append_button' => [
                        'class_name' => 'generateQuestion',
                        'theme' => 'success',
                        'word' => '產生問題',
                    ],
                ],
                [
                    'title' => '生成數量',
                    'type' => 'text',
                    'name' => 'count',
                    'placeholder' => '請輸入生成數量',
                    'required' => false,
                    'id' => 'count',
                    'class' => 'col-10',
                    'warning_word' => '(最多 10 則)',
                ],
                [
                    'title' => '生成問題',
                    'type' => 'selectize',
                    'name' => 'questions',
                    'required' => false,
                    'id' => 'questions',
                    'class' => 'col-10',
                    'warning_word' => '(可拖拉問題進行排序)',
                ],
                [
                    'title' => '開始時間',
                    'type' => 'custome-date-start',
                    'name' => 'start_at',
                    'placeholder' => '請輸入開始時間',
                    'required' => true,
                    'id' => 'start_at',
                    'class' => 'col-10',
                    'warning_word' => null,
                ],
                [
                    'title' => '結束時間',
                    'type' => 'custome-date-end',
                    'name' => 'end_at',
                    'placeholder' => '請輸入結束時間',
                    'required' => true,
                    'id' => 'end_at',
                    'class' => 'col-10',
                    'warning_word' => null,
                ],
            ],
        ],
        'aiwize' => [
            'edit_field' => [
                [
                    'title' => 'AI Wize ID',
                    'type' => 'show',
                    'name' => 'ai_wize_id',
                    'placeholder' => '',
                    'required' => false,
                    'id' => 'ai_wize_id',
                    'class' => 'col-10',
                    'warning_word' => null,
                ],
                [
                    'title' => 'AI Wize 發佈時間',
                    'type' => 'show',
                    'name' => 'ai_wize_publish',
                    'placeholder' => '',
                    'required' => false,
                    'id' => 'ai_wize_publish',
                    'class' => 'col-10',
                    'warning_word' => null,
                ],
                [
                    'title' => '健康文章ID',
                    'type' => 'show',
                    'name' => 'health_article_id',
                    'placeholder' => '',
                    'required' => false,
                    'id' => 'health_article_id',
                    'class' => 'col-10',
                    'warning_word' => null,
                ],
                [
                    'title' => '長標題',
                    'type' => 'select',
                    'name' => 'long_title',
                    'placeholder' => '',
                    'required' => false,
                    'id' => 'long_title',
                    'class' => 'col-12',
                    'warning_word' => '下拉選擇長標題',
                    'option' => [],
                ],
                [
                    'title' => '短標題',
                    'type' => 'select',
                    'name' => 'short_title',
                    'placeholder' => '',
                    'required' => false,
                    'id' => 'short_title',
                    'class' => 'col-12',
                    'warning_word' => '下拉選擇短標題',
                    'option' => [],
                ],
                [
                    'title' => '內容',
                    'type' => 'div',
                    'name' => 'content',
                    'placeholder' => '',
                    'required' => false,
                    'id' => 'content',
                    'class' => 'col-12',
                    'warning_word' => null,
                ],
                [
                    'title' => '關鍵字',
                    'type' => 'show',
                    'name' => 'keyword',
                    'placeholder' => '',
                    'required' => false,
                    'id' => 'keyword',
                    'class' => 'col-12',
                    'warning_word' => null,
                ],
            ],
        ],
    ],
];