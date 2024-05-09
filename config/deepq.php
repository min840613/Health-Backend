<?php

return [
    'api' => [
        'article_update_url' => env('DEEPQ_ARTICLE_UPDATE_URL'),
        'article_recommend_url' => env('DEEPQ_ARTICLE_RECOMMEND_URL'),
        'token' => env('DEEPQ_TOKEN')
    ],
    'aws' => [
        's3_folder' => env('AWS_S3_DEEP_FOLDER')
    ]
];
