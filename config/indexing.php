<?php

return [
    'default' => env('INDEXING_DRIVER', 'log'),
    'service_account_file' => env('INDEXING_FILE'),
    'need_log' => env('INDEXING_NEED_LOG', false),
    'token_name' => env('INDEXING_TOKEN_NAME', 'google_indexing_access_token'),
];
