<?php

return [
    'aws' => [
        'key_id' => env('AWS_ACCESS_KEY_ID'),
        'key_secret' => env('AWS_SECRET_ACCESS_KEY'),
        'default_region' => env('AWS_DEFAULT_REGION'),
    ],
    'cdn' => [
        'url' => env('AWS_S3_URL'),
        'distribution_id' => env('AWS_CLOUDFRONT_DISTRIBUTION_ID'),
    ],
    's3' => [
        'path' => env('AWS_S3_PATH'),
        'video_path' => env('AWS_S3_VIDEO_PATH'),
    ],
    'frontend_url'  =>  env('APP_WEB_URL','https://health.tvbs.com.tw')
];
