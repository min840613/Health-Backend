<?php

return [
    'sns' => [
        'version' => env('SNS_VERSION'),
        'region' => env('SNS_REGION'),
        'credentials' => [
            'key' => env('SNS_CREDENTIALS_KEY'),
            'secret' => env('SNS_CREDENTIALS_SECRET'),
        ],
        'arn' => env('SNS_ARN'),
    ],
];
